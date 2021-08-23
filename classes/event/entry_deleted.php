<?php

namespace mod_cpdlogbook\event;

class entry_deleted extends entry_created {

    public function init() {
        parent::init();
        $this->data['crud'] = 'd';
    }
}