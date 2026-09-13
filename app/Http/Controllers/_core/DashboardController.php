<?php

namespace App\Http\Controllers\_core;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->data['breadcrumbs'] = [];
        $this->data['title'] = 'Dashboard';
        $this->data['notification'] = (object) [
            'unread' => 0,
            'notifications' => [],
        ];
    }

    protected function addBreadcrumb($name, $url = null)
    {
        $this->data['breadcrumbs'][] = (object) [
            'name' => $name,
            'url' => $url,
        ];
    }

    protected function getBreadcrumbs()
    {
        return $this->data['breadcrumbs'];
    }

    protected function setTitle($title)
    {
        $this->data['title'] = $title;
    }

    protected function getTitle()
    {
        return $this->data['title'];
    }

    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function getData($key)
    {
        return $this->data[$key];
    }
}
