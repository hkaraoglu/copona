<?php
class ControllerExtensionModuleBanner extends Controller {

    public function index($setting) {
        static $module = 0;

        $this->load->model('design/banner');
        $this->load->model('tool/image');

        // Needed to replace $this->config->get('theme_name') with "default", to load files from "default" folder.
        // then replaced to "addets/.. " :) to allow control in document class.

        $this->document->addStyle('assets/vendor/owl-carousel/owl.carousel.css');
        $this->document->addStyle('assets/vendor/owl-carousel/owl.transitions.css');
        $this->document->addScript('assets/vendor/owl-carousel/owl.carousel.min.js');

        $data['banners'] = array();

        $results = $this->model_design_banner->getBanner($setting['banner_id']);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $data['banners'][] = array(
                    'title' => $result['title'],
                    'link'  => $result['link'],
                    'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
                );
            }
        }

        $data['module'] = $module++;

        return $this->load->view('extension/module/banner', $data);
    }

}