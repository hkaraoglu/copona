<?php
class ControllerCommonFileManager extends Controller {
    public function index() {
        $this->load->language('common/filemanager');

        // Find which protocol to use to pass the full image link back
        if ($this->request->server['HTTPS']) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = rtrim(str_replace('*', '', $this->request->get['filter_name']), '/');
        } else {
            $filter_name = null;
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace('*', '', $this->request->get['directory']), '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $directories = array();
        $files = array();

        $data['images'] = array();

        $this->load->model('tool/image');

        if (substr(str_replace('\\', '/', realpath($directory) . '/' . $filter_name), 0, strlen(DIR_IMAGE . 'catalog')) == str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
            // Get directories
            $directories = glob($directory . '/*' . $filter_name . '*', GLOB_ONLYDIR);

            if (!$directories) {
                $directories = array();
            }

            // Get files
            $files = glob($directory . '/*' . $filter_name . '*', GLOB_BRACE);
            $extension_allowed = preg_replace('~\r?\n~', "\n", Config::get('config_file_ext_allowed'));
            $allowed = implode("|", explode("\n", $extension_allowed) );
            $files = preg_grep('~\.('.$allowed.')$~i', $files);


            if (!$files) {
                $files = array();
            }
        }

        // Merge directories and files
        $images = array_merge($directories, $files);

        // Get total number of files and directories
        $image_total = count($images);

        // Split the array based on current page number and max number of items per page of 10
        $images = array_splice($images, ($page - 1) * 24, 24);

        foreach ($images as $image) {
            $name = basename($image);

            if (is_dir($image)) {
                $url = '';

                if (isset($this->request->get['target'])) {
                    $url .= '&target=' . $this->request->get['target'];
                }

                if (isset($this->request->get['thumb'])) {
                    $url .= '&thumb=' . $this->request->get['thumb'];
                }

                if (isset($this->request->get['ckedialog'])) {
                    $url .= '&ckedialog=' . $this->request->get['ckedialog'];
                }
                $data['images'][] = array(
                    'thumb' => '',
                    'name'  => $name,
                    'type'  => 'directory',
                    'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
                    'href'  => $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . '&directory=' . urlencode(utf8_substr($image, utf8_strlen(DIR_IMAGE . 'catalog/'))) . $url, true)
                );
            } elseif (substr(str_replace('\\', '/', realpath($image)), 0, utf8_strlen(DIR_IMAGE . 'catalog')) == DIR_IMAGE . 'catalog') {

                $name = basename($image);

                $path = utf8_substr($image, utf8_strlen(DIR_IMAGE));
                if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) == 'svg') {
                    $thumb = HTTP_CATALOG . 'image/' . $path;
                } else {
                    $thumb = $this->model_tool_image->resize($path, 136, 136);
                }


                $data['images'][] = array(
                    'thumb' => $thumb,
                    'name'  => $name,
                    'type'  => 'image',
                    'path'  => $path,
                    'href'  => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
                );
            }
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['entry_search'] = $this->language->get('entry_search');
        $data['entry_folder'] = $this->language->get('entry_folder');

        $data['button_parent'] = $this->language->get('button_parent');
        $data['button_refresh'] = $this->language->get('button_refresh');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['button_folder'] = $this->language->get('button_folder');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_search'] = $this->language->get('button_search');

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['directory'])) {
            $data['directory'] = urlencode($this->request->get['directory']);
        } else {
            $data['directory'] = '';
        }

        if (isset($this->request->get['filter_name'])) {
            $data['filter_name'] = $this->request->get['filter_name'];
        } else {
            $data['filter_name'] = '';
        }

        // Return the target ID for the file manager to set the value
        if (isset($this->request->get['target'])) {
            $data['target'] = $this->request->get['target'];
        } else {
            $data['target'] = '';
        }

        // Return the thumbnail for the file manager to show a thumbnail
        if (isset($this->request->get['thumb'])) {
            $data['thumb'] = $this->request->get['thumb'];
        } else {
            $data['thumb'] = '';
        }

        if (isset($this->request->get['ckedialog'])) {
            $data['ckedialog'] = $this->request->get['ckedialog'];
        } else {
            $data['ckedialog'] = '';
        }

        // ckeditor
        if (isset($this->request->get['ckedialog'])) {
            $data['ckedialog'] = $this->request->get['ckedialog'];
        } else {
            $data['ckedialog'] = '';
        }
        // Parent
        $url = '';

        if (isset($this->request->get['directory'])) {
            $pos = strrpos($this->request->get['directory'], '/');

            if ($pos) {
                $url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
            }
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $data['parent'] = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url, true);

        // Refresh
        $url = '';

        if (isset($this->request->get['directory'])) {
            $url .= '&directory=' . urlencode($this->request->get['directory']);
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $data['refresh'] = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url, true);

        $url = '';

        if (isset($this->request->get['directory'])) {
            $url .= '&directory=' . urlencode(html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $pagination = new Pagination();
        $pagination->total = $image_total;
        $pagination->page = $page;
        $pagination->limit = 24;
        $pagination->url = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        if (isset($this->request->get['view']) && $this->request->get['view'] == 'list') {
            $this->response->setOutput($this->load->view('common/filemanager_list', $data));
        } else {
            $this->response->setOutput($this->load->view('common/filemanager', $data));
        }
    }

    public function upload() {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog')) != DIR_IMAGE . 'catalog') {
            $json['error'] = $this->language->get('error_directory');
        }

        if (!$json) {
            // Check if multiple files are uploaded or just one
            $files = array();

            $max = return_bytes(ini_get('post_max_size'));
            if ($max < $_SERVER['CONTENT_LENGTH']) {
                $json['error'] = $this->language->get('error_filesize');
                $json['error'] .= "\n" . sprintf($this->language->get('error_filesize_allowed'), ini_get('post_max_size'));
            }

            if (!empty($this->request->files['file']['name']) && is_array($this->request->files['file']['name'])) {
                foreach (array_keys($this->request->files['file']['name']) as $key) {
                    $files[] = array(
                        'name'     => $this->request->files['file']['name'][$key],
                        'type'     => $this->request->files['file']['type'][$key],
                        'tmp_name' => $this->request->files['file']['tmp_name'][$key],
                        'error'    => $this->request->files['file']['error'][$key],
                        'size'     => $this->request->files['file']['size'][$key]
                    );
                }
            }

            foreach ($files as $file) {
                if (is_file($file['tmp_name'])) {
                    // Sanitize the filename
                    $filename = basename(html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8'));

                    // Validate the filename length
                    if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                        $json['error'] = $this->language->get('error_filename');
                    }

                    // Allowed file extension types
                    $extension_allowed = preg_replace('~\r?\n~', "\n", Config::get('config_file_ext_allowed'));
                    $allowed = explode("\n", $extension_allowed);
                    $extension = utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1));

                    if($extension != 'svg'){
                        if (!in_array($extension, $allowed)) {
                            $json['error'] = $this->language->get('error_filextension');
                        }


                        // Allowed file mime types
                        $mime_allowed = preg_replace('~\r?\n~', "\n", Config::get('config_file_mime_allowed'));
                        $allowed = explode("\n", $mime_allowed);

                        if (!in_array($file['type'], $allowed)) {
                            $json['error'] = $this->language->get('error_filemime');
                        }
                    }
                    // Return any upload error
                    if ($file['error'] != UPLOAD_ERR_OK) {
                        $json['error'] = $this->language->get('error_upload_' . $file['error']);
                    }


                } else {
                    $json['error'] = $this->language->get('error_upload');
                }

                if (!$json) {


                    // Copona: add suffix, if filename exists
                    if (file_exists($directory . '/' . $filename)) {
                        $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
                        for ($i = 1; $i < 100; $i++) {
                            $filename_new = preg_replace('/^(.*)\.' . $filename_ext . '$/', '$1_' . $i . "." . $filename_ext, $filename);
                            if (!file_exists($directory . '/' . $filename_new)) {
                                $filename = $filename_new;
                                break;
                            }
                        }
                    }

                    move_uploaded_file($file['tmp_name'], $directory . '/' . $filename);
                }
            }
        }

        if (!$json) {
            $json['success'] = $this->language->get('text_uploaded') . "\n\nFilename: $filename";
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function folder() {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog')) != DIR_IMAGE . 'catalog') {
            $json['error'] = $this->language->get('error_directory');
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // Sanitize the folder name
            $folder = basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
                $json['error'] = $this->language->get('error_folder');
            }

            // Check if directory already exists or not
            if (is_dir($directory . '/' . $folder)) {
                $json['error'] = $this->language->get('error_exists');
            }
        }

        if (!isset($json['error'])) {
            mkdir($directory . '/' . $folder, 0777);
            if (file_exists($directory . '/' . $folder)) {
                chmod($directory . '/' . $folder, 0777);
                @touch($directory . '/' . $folder . '/' . 'index.html');

                $json['success'] = $this->language->get('text_directory');
            } else {
                $json['error'] = "ERROR: Folder \n" . $directory . '/' . $folder . "\n not created!";
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete() {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['path'])) {
            $paths = $this->request->post['path'];
        } else {
            $paths = array();
        }

        // Loop through each path to run validations
        foreach ($paths as $path) {
            // Check path exsists
            if ($path == DIR_IMAGE . 'catalog' || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $path)), 0, strlen(DIR_IMAGE . 'catalog')) != DIR_IMAGE . 'catalog') {
                $json['error'] = $this->language->get('error_delete');

                break;
            }
        }

        if (!$json) {
            // Loop through each path
            foreach ($paths as $path) {
                $path = rtrim(DIR_IMAGE . $path, '/');

                // If path is just a file delete it
                if (is_file($path)) {
                    unlink($path);

                    // If path is a directory beging deleting each file and sub folder
                } elseif (is_dir($path)) {
                    $files = array();

                    // Make path into an array
                    $path = array($path . '*');

                    // While the path array is still populated keep looping through
                    while (count($path) != 0) {
                        $next = array_shift($path);

                        foreach (glob($next) as $file) {
                            // If directory add to path array
                            if (is_dir($file)) {
                                $path[] = $file . '/*';
                            }

                            // Add the file to the files to be deleted array
                            $files[] = $file;
                        }
                    }

                    // Reverse sort the file array
                    rsort($files);

                    foreach ($files as $file) {
                        // If file just delete
                        if (is_file($file)) {
                            unlink($file);

                            // If directory use the remove directory function
                        } elseif (is_dir($file)) {
                            rmdir($file);
                        }
                    }
                }
            }

            $json['success'] = $this->language->get('text_delete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
