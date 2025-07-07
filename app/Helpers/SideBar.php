
<?php

namespace App\Helpers;

use Framework\Support\Config;

class SideBar
{
    public function render()
    {
        $sideBarIcons = Config::get('sidebar.icons');
        $sideBarWithLogin = Config::get('sidebar.login');

        // Your sidebar rendering logic here...
        echo "Sidebar Icons: $sideBarIcons";
        echo "Sidebar with Login: $sideBarWithLogin";
    }
}
