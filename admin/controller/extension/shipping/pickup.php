<?php
class ControllerExtensionShippingPickup extends Controller {
    private $error = array();

    public function index() {
        $data = $this->load->language('extension/shipping/pickup');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('pickup', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/pickup', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/shipping/pickup', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true);

        if (isset($this->request->post['pickup_name'])) {
            $data['pickup_name'] = $this->request->post['pickup_name'];
        } else {
            $data['pickup_name'] = $this->config->get('pickup_name');
        }

        if (isset($this->request->post['pickup_geo_zone_id'])) {
            $data['pickup_geo_zone_id'] = $this->request->post['pickup_geo_zone_id'];
        } else {
            $data['pickup_geo_zone_id'] = $this->config->get('pickup_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['pickup_status'])) {
            $data['pickup_status'] = $this->request->post['pickup_status'];
        } else {
            $data['pickup_status'] = $this->config->get('pickup_status');
        }

        if (isset($this->request->post['pickup_sort_order'])) {
            $data['pickup_sort_order'] = $this->request->post['pickup_sort_order'];
        } else {
            $data['pickup_sort_order'] = $this->config->get('pickup_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/shipping/pickup', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/shipping/pickup')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}