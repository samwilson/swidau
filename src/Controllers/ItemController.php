<?php

namespace App\Controllers;

use App\App;
use App\Config;
use App\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ItemController extends Base {

    public function index(Request $request, Response $response, array $args) {
        $items = $this->db->query("SELECT id, title, description FROM items WHERE auth_level = 0 ORDER BY RAND() LIMIT 10");
        $template = new \App\Template('home.twig');
        $template->items = $items;
        $template->title = 'Home';
        $response->setContent($template->render());
        return $response;
    }

    public function view(Request $request, Response $response, array $args) {
        $item = new Item($args['id']);
        $template = new \App\Template('view.twig');
        $template->item = $item;
        $template->title = $item->getTitle();
        $template->tags = $item->getTags();
        $template->mime_type = $item->getMimeType();
        // Return the template.
        $response->setContent($template->render());
        return $response;
    }

    public function edit(Request $request, Response $response, array $args) {
        $template = new \App\Template('form.twig');
        $template->title = 'Create';
        if (!$this->user) {
            $template->alert('info', 'You have to <a href="'.$this->config->baseUrl().'/login" class="alert-link">log in</a> before you can add or edit items.');
        }
        $item = new Item();
        if (isset($args['id'])) {
            $template->title = 'Editing #' . $args['id'];
            $item = new Item($args['id']);
        }
        $template->date_granularities = $this->db->query("SELECT id, title FROM date_granularities ORDER BY id ASC")->fetchAll();
        $template->groups = $this->db->query("SELECT `id`, `name` FROM `groups` ORDER BY id ASC")->fetchAll();

        $template->item = $item;
        $template->fileContents = $item->getFileContents();
        $response->setContent($template->render());
        return $response;
    }

    public function save(Request $request, Response $response, array $args) {
        $_POST = array_filter($_POST, 'trim');
        $metadata = array(
            'id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT),
            'title' => filter_input(INPUT_POST, 'title'),
            'description' => filter_input(INPUT_POST, 'description'),
            'date' => filter_input(INPUT_POST, 'date'),
            'date_granularity' => filter_input(INPUT_POST, 'date_granularity', FILTER_SANITIZE_NUMBER_INT),
            'auth_level' => filter_input(INPUT_POST, 'auth_level', FILTER_SANITIZE_NUMBER_INT),
        );
        $tags = filter_input(INPUT_POST, 'tags');
        $item = new \App\Item();
        $item->save($metadata, $tags, $_FILES['file']['tmp_name'], filter_input(INPUT_POST, 'file_contents'));

        $config = new Config();
        return new RedirectResponse($config->baseUrl() . '/' . $item->getId());
    }

}
