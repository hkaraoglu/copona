<?php
class ControllerCatalogCategory extends Controller {
    private $error = array();

    public function index() {

        $data = array_merge(array(), $this->load->language('catalog/category'));

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/category');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $category_id = $this->model_catalog_category->addCategory($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            // $this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
            if (isset($this->request->post['save_continue']) and $this->request->post['save_continue'])
                $this->response->redirect($this->url->link('catalog/category/edit', 'category_id=' . $category_id . '&token=' . $this->session->data['token'] . $url, true));
            else
                $this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $category_id = $this->request->get['category_id'];
            $this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_parent'])) {
                $url .= '&filter_parent=' . urlencode(html_entity_decode($this->request->get['filter_parent'], ENT_QUOTES, 'UTF-8'));
            }


            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->post['save_continue']) and $this->request->post['save_continue'])
                $this->response->redirect($this->url->link('catalog/category/edit', 'category_id=' . $category_id . '&token=' . $this->session->data['token'] . $url, true));
            else
                $this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/category');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_catalog_category->deleteCategory($category_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getList();
    }

    public function repair() {
        $this->load->language('catalog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/category');

        if ($this->validateRepair()) {
            $this->model_catalog_category->repairCategories();

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        $data = array_merge(array(), $this->load->language('catalog/category'));

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_parent'])) {
            $filter_parent = $this->request->get['filter_parent'];
        } else {
            $filter_parent = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_parent'])) {
            $url .= '&filter_parent=' . urlencode(html_entity_decode($this->request->get['filter_parent'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true)
        );

        $data['add'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . $url, true);
        $data['repair'] = $this->url->link('catalog/category/repair', 'token=' . $this->session->data['token'] . $url, true);

        $data['categories'] = array();

        $filter_data = array(
            'filter_name'   => $filter_name,
            'filter_parent' => $filter_parent,
            'sort'          => $sort,
            'order'         => $order,
            'start'         => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'         => $this->config->get('config_limit_admin')
        );

        $category_total = $this->model_catalog_category->getTotalCategories($filter_data);

        $results = $this->model_catalog_category->getCategories($filter_data);

        foreach ($results as $result) {
            $data['categories'][] = array(
                'category_id' => $result['category_id'],
                'name'        => $result['name'],
                'sort_order'  => $result['sort_order'],
                'edit'        => $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, true),
                'delete'      => $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_rebuild'] = $this->language->get('button_rebuild');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_parent'])) {
            $url .= '&filter_parent=' . urlencode(html_entity_decode($this->request->get['filter_parent'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_parent'])) {
            $url .= '&filter_parent=' . urlencode(html_entity_decode($this->request->get['filter_parent'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        $data['filter_parent'] = $filter_parent;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/category_list', $data));
    }

    protected function getForm() {
        $data = array_merge(array(), $this->load->language('catalog/category'));

        $data['text_form'] = !isset($this->request->get['category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        if (isset($this->error['parent'])) {
            $data['error_parent'] = $this->error['parent'];
        } else {
            $data['error_parent'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_parent'])) {
            $url .= '&filter_parent=' . urlencode(html_entity_decode($this->request->get['filter_parent'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true)
        );

        if (!isset($this->request->get['category_id'])) {
            $data['action'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true);

        if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
        }

        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['category_description'])) {
            $data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($this->request->get['category_id']);
        } else {
            $data['category_description'] = array();
        }

        if (isset($this->request->post['path'])) {
            $data['path'] = $this->request->post['path'];
        } elseif (!empty($category_info)) {
            $data['path'] = $category_info['path'];
        } else {
            $data['path'] = '';
        }

        if (isset($this->request->post['parent_id'])) {
            $data['parent_id'] = $this->request->post['parent_id'];
        } elseif (!empty($category_info)) {
            $data['parent_id'] = $category_info['parent_id'];
        } else {
            $data['parent_id'] = 0;
        }

        $this->load->model('catalog/filter');

        if (isset($this->request->post['category_filter'])) {
            $filters = $this->request->post['category_filter'];
        } elseif (isset($this->request->get['category_id'])) {
            $filters = $this->model_catalog_category->getCategoryFilters($this->request->get['category_id']);
        } else {
            $filters = array();
        }

        $data['category_filters'] = array();

        foreach ($filters as $filter_id) {
            $filter_info = $this->model_catalog_filter->getFilter($filter_id);

            if ($filter_info) {
                $data['category_filters'][] = array(
                    'filter_id' => $filter_info['filter_id'],
                    'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
                );
            }
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['category_store'])) {
            $data['category_store'] = $this->request->post['category_store'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_store'] = $this->model_catalog_category->getCategoryStores($this->request->get['category_id']);
        } else {
            $data['category_store'] = array( 0 );
        }

        if (isset($this->request->post['seo_keywords'])) {
            $data['seo_keywords'] = $this->request->post['seo_keywords'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['seo_keywords'] = $this->seourl->getSeoUrls('category_id=' . $this->request->get['category_id']);
        } else {
            $data['seo_keywords'] = '';
        }

        if (isset($this->request->post['keyword'])) {
            $data['keyword'] = $this->request->post['keyword'];
        } elseif (!empty($category_info)) {
            $data['keyword'] = $category_info['keyword'];
        } else {
            $data['keyword'] = '';
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($category_info)) {
            $data['image'] = $category_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['top'])) {
            $data['top'] = $this->request->post['top'];
        } elseif (!empty($category_info)) {
            $data['top'] = $category_info['top'];
        } else {
            $data['top'] = 0;
        }

        if (isset($this->request->post['column'])) {
            $data['column'] = $this->request->post['column'];
        } elseif (!empty($category_info)) {
            $data['column'] = $category_info['column'];
        } else {
            $data['column'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($category_info)) {
            $data['sort_order'] = $category_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($category_info)) {
            $data['status'] = $category_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['category_layout'])) {
            $data['category_layout'] = $this->request->post['category_layout'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_layout'] = $this->model_catalog_category->getCategoryLayouts($this->request->get['category_id']);
        } else {
            $data['category_layout'] = array();
        }

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/category_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->get['category_id']) && $this->request->post['parent_id']) {
            $results = $this->model_catalog_category->getCategoryPath($this->request->post['parent_id']);

            foreach ($results as $result) {
                if ($result['path_id'] == $this->request->get['category_id']) {
                    $this->error['parent'] = $this->language->get('error_parent');

                    break;
                }
            }
        }

        if (utf8_strlen($this->request->post['keyword']) > 0) {
            $this->load->model('catalog/url_alias');

            $url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

            if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'category_id=' . $this->request->get['category_id']) {
                $this->error['keyword'] = sprintf($this->language->get('error_keyword'));
            }

            if ($url_alias_info && !isset($this->request->get['category_id'])) {
                $this->error['keyword'] = sprintf($this->language->get('error_keyword'));
            }

            if ($this->error && !isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_warning');
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateRepair() {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/category');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort'        => 'name',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => Config::get('admin_category_autocomplete_limit', 15),
            );

            $results = $this->model_catalog_category->getCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}