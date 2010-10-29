<?php
class PP_Decorator_Label
{
	public function render($content = '')
    {
        $output = "<a href=\"errors\">$content</div>";
agasdgsdg
        $placement = $this->getPlacement();
        $separator = $this->getSeparator();

        switch ($placement) {
            case 'PREPEND':
                return $output . $separator . $content;
            case 'APPEND':
            default:
                return $content . $separator . $output;
        }
    }

}
