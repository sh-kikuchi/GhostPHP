<?php

namespace app\form_classes;

use app\aura\utils\Session;
use app\aura\utils\Validator;
use app\aura\https\Redirect;
use app\repositories\PostRepository;
use app\aura\database\DataBaseConnect;

require_once 'interfaces\form_classes\IPostRequest.php';

/**
 * Class PostRequest
 *
 * Handles post request data and validation for creating and updating posts.
 */
class PostRequest implements IPostRequest {

    protected ?int    $id;
    protected ?int    $user_id;
    protected ?string $title;
    protected ?string $body;

    /**
     * Constructor to initialize post request data.
     *
     * @param array|null $data Associative array of post data.
     */
    function __construct(?array $data) {
        if (!isset($data['delete'])) {
            $this->validate($data);
        }
        $this->id          = $data['id']      ?? 0;
        $this->user_id     = $data['user_id'] ?? 0;
        $this->title       = $data['title']    ?? '';
        $this->body        = $data['body']    ?? '';
    }

    /** Setter methods */

    /**
     * Set the post ID.
     *
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Set the user ID.
     *
     * @param int $user_id
     */
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    /**
     * Set the post title.
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Set the post body.
     *
     * @param string $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /** Getter methods */

    /**
     * Get the post ID.
     *
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the user ID.
     *
     * @return int|null
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * Get the post title.
     *
     * @return string|null
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Get the post body.
     *
     * @return string|null
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Get an associative array of the post request data.
     *
     * @return array
     */
    public function getArrayData() {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'title'       => $this->title,
            'body'      => $this->body,
        ];
    }

    /**
     * Validate POST data for the post request.
     *
     * @param array $targetData The data to validate.
     * @return void
     */
    public function validate($targetData) {
        unset($_SESSION['errors']);
        $validator = new Validator;
        $session   = new Session;
        $post_title = $targetData['title'];

        /**
         * Validate the title field.
         * @param string $post_title The title of the post.
         * @param string $field The field name for error reporting.
         * @param bool $required Whether the field is required.
         * @param int|null $minLength Minimum length of the field.
         * @param int|null $maxLength Maximum length of the field.
         */
        $validator->validateString($post_title, 'title', true, 0, 100);

        $errors = $validator->getErrors();

        
        if ($errors !== [] && count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $session->oldPostValue($targetData);
            if (!empty($targetData['id'])) {
                $param = '?id=' . $targetData['id'];
                Redirect::to('post/update' . $param);
                exit();
            } else {
                Redirect::to('post/create');
                exit();
            }
        }
    }
}
?>
