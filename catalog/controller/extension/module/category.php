<?php

class ControllerExtensionModuleCategory extends Controller
{
    private $language_id;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->language_id = $registry->config->get('config_language_id');
    }

    public function index()
    {

        $data = array_merge(array(), $this->language->load('extension/module/category'));
        $data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        if (isset($parts[0])) {
            $data['category_id'] = $parts[0];
        } else {
            $data['category_id'] = 0;
        }

        if (isset($parts[1])) {
            $data['child_id'] = $parts[1];
        } else {
            $data['child_id'] = 0;
        }

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            $children_data = $this->getChildren($category['category_id']);

            $filter_data = array(
                'filter_category_id'  => $category['category_id'],
                'filter_sub_category' => true
            );

            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name'        => $category['name'],
                'total'       => $this->config->get('config_product_count') ? $this->model_catalog_product->getTotalProducts($filter_data) : false,
                'children'    => $children_data,
                'href'        => $this->url->link('product/category', 'path=' . $category['category_id']),
                'active'      => (in_array($category['category_id'], $parts) ? true : false)
            );
        }

        $data['category_path'] = $parts;

        return $this->load->view('extension/module/category', $data);
    }

    function getChildren($category_id, $children_data = array())
    {
        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        $this->load->model('catalog/category');

        $children = $this->model_catalog_category->getCategories($category_id);

        foreach ($children as $child) {
            $children_data[] = array(
                'category_id' => $child['category_id'],
                'name'        => $child['name'],
                'children'    => $this->getChildren($child['category_id']),
                'total'       => ($this->config->get('config_product_count') ? $this->model_catalog_product->getTotalProducts(['filter_category_id' => $child['category_id']]) : ''),
                'href'        => $this->url->link('product/category',
                    'path=' . $this->model_catalog_category->getCategoryPath($child['category_id'])),
                'active'      => (in_array($child['category_id'], $parts) ? true : false),
                'level'       => count(explode('_',
                    (string)$this->model_catalog_category->getCategoryPath($child['category_id'])))
            );
        }

        return $children_data;
    }

}