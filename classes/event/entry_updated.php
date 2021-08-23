<?php

namespace mod_cpdlogbook\event;

class entry_updated extends entry_created {

    public function init() {
        parent::init();
        $this->data['crud'] = 'u';
    }
}