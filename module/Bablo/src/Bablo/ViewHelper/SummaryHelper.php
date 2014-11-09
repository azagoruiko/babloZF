<?php

namespace Bablo\ViewHelper;

use Zend\View\Helper\AbstractHelper;
class SummaryHelper extends AbstractHelper {
    private static $wait = <<<EOD
        <div>
            <img src="/img/indicator.gif" />
        </div>
        <div>
            Loading...
        </div>
EOD;
    private static $func = <<<EOD
    $().ready(function() {
        setInterval( function () {
            if (!%handler%inProgress) {
                %handler%inProgress = true;

                $.post('%url%', 
                    {},
                    %handler%
                );
            }
        }, 3000);
    });
EOD;
    public function __invoke($id, $cssClass, $url, $handler) {
        $this->getView()->inlineScript()->appendScript(
                str_replace('%handler%', $handler, str_replace('%url%', $url, self::$func))
                );
        return "<div id=\"$id\" class=\"$cssClass\">" .self::$wait . "</div>\n";
    }
}
