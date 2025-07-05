<?php

/**
 * Class Response
 *
 * Represents an HTTP response, including content, status code, and headers.
 */
class Response {

    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();
    protected $protocol_version;

    /**
     * Constructor to initialize the response with an optional HTTP protocol version.
     *
     * @param string $protocol_version HTTP protocol version (default is '1.1').
     */
    public function __construct($protocol_version = '1.1') {
        $this->protocol_version = $protocol_version;
        ob_start();
    }

    /**
     * Sends the HTTP response to the client.
     *
     * @return void
     */
    public function send() {
        header($this->protocol_version . ' ' . $this->status_code . ' ' . $this->status_text);

        foreach ($this->http_headers as $key => $val) {
            header($key . ': ' . $val);
        }

        echo $this->content;
    }

    /**
     * Sets the content of the response and the corresponding content type.
     *
     * @param mixed $content The content to be sent in the response.
     * @param string $contentType The MIME type of the content (default is 'text/html').
     * @return void
     */
    public function setContent($content, $contentType = 'text/html') {
        $this->content = $content;
        $this->setHttpHeader('Content-Type', $contentType);
    }

    /**
     * Sets the HTTP status code and optional status text for the response.
     *
     * @param int $status_code The HTTP status code to set.
     * @param string $status_text The optional status text to set (default is an empty string).
     * @return void
     */
    public function setStatusCode($status_code, $status_text = '') {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }

    /**
     * Sets an HTTP header for the response.
     *
     * @param string $header The header name.
     * @param string $val The header value.
     * @return void
     */
    public function setHttpHeader($header, $val) {
        $this->http_headers[$header] = $val;
    }
}
