<?php
    class recurring {
        protected $slug = 'netopia_recurring';

        function getSignature() {
            return get_option($this->slug.'_signature', array());
        }

        function getApiKey() {
            return get_option($this->slug.'_api_key', array());
        }
    }
?>