<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Database;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            $this->session->flash('error', '请先登录');
            return $this->response->redirect('/login');
        }

        $db = Database::getInstance();
        $user = $db->queryOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $addresses = $db->query("SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, id DESC", ['user_id' => $userId]);

        return $this->view('profile/edit', [
            'user' => $user,
            'addresses' => $addresses,
        ]);
    }

    public function update(): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            $this->session->flash('error', '请先登录');
            return $this->response->redirect('/login');
        }

        $name = $this->request->post('name');
        $phone = $this->request->post('phone');
        $avatar = $this->request->post('avatar');

        $db = Database::getInstance();
        $db->update('users', [
            'name' => $name,
            'phone' => $phone,
            'avatar' => $avatar,
        ], 'id = :id', ['id' => $userId]);

        // 更新 session 中的用户信息
        $user = $db->queryOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $this->session->set('user', $user);

        $this->session->flash('success', '个人信息已更新');
        return $this->response->redirect('/profile');
    }

    public function addAddress(): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $data = $this->request->all();
        $data['user_id'] = $userId;

        // 如果是默认地址，先取消其他默认地址
        if (!empty($data['is_default'])) {
            $db = Database::getInstance();
            $db->update('addresses', ['is_default' => 0], 'user_id = :user_id', ['user_id' => $userId]);
        }

        $db = Database::getInstance();
        $db->insert('addresses', $data);

        $this->session->flash('success', '地址已添加');
        return $this->response->redirect('/profile');
    }

    public function updateAddress(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $addressId = $id;
        $data = $this->request->all();

        // 如果是默认地址，先取消其他默认地址
        if (!empty($data['is_default'])) {
            $db = Database::getInstance();
            $db->update('addresses', ['is_default' => 0], 'user_id = :user_id AND id != :id', [
                'user_id' => $userId,
                'id' => $addressId,
            ]);
        }

        $db = Database::getInstance();
        $db->update('addresses', $data, 'id = :id AND user_id = :user_id', [
            'id' => $addressId,
            'user_id' => $userId,
        ]);

        $this->session->flash('success', '地址已更新');
        return $this->response->redirect('/profile');
    }

    public function deleteAddress(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $addressId = $id;
        $db = Database::getInstance();
        $db->delete('addresses', 'id = :id AND user_id = :user_id', [
            'id' => $addressId,
            'user_id' => $userId,
        ]);

        $this->session->flash('success', '地址已删除');
        return $this->response->redirect('/profile');
    }

    public function setDefaultAddress(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $addressId = $id;
        $db = Database::getInstance();

        // 先取消所有默认地址
        $db->update('addresses', ['is_default' => 0], 'user_id = :user_id', ['user_id' => $userId]);
        
        // 设置当前地址为默认
        $db->update('addresses', ['is_default' => 1], 'id = :id AND user_id = :user_id', [
            'id' => $addressId,
            'user_id' => $userId,
        ]);

        $this->session->flash('success', '默认地址已设置');
        return $this->response->redirect('/profile');
    }
}
