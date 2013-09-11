<?php

    /**
     * Этот файл часть фреймворка DM Framework
     * Любое использование в коммерческих целях допустимо лишь при разрешении автора.
     *
     * @author damirazo <me@damirazo.ru>
     */

    namespace DMF\Core\Template\Tag\UrlTag;

    use DMF\Core\Application\Application;
    use DMF\Core\Http\Request;
    use DMF\Core\Template\BaseTagNode;

    /**
     * Class UrlTagNode
     *
     * @package DMF\Core\Template\Tag\UrlTag
     */
    class UrlTagNode extends BaseTagNode
    {

        public function compile(\Twig_Compiler $compiler)
        {
            // Получаем объект маршрута, соответствующий переданному экшену
            $route = Application::get_route_by_path($this->getAttribute('path'));
            // Получаем список переданных аргументов
            $args = $this->getAttribute('args');
            // Строка с маршрутом
            $result = $route->pattern;

            // Производим замену динамических сегментов маршрута на переданные значения
            $count = true;
            while ($count) {
                $value = array_shift($args);
                $replacement = '".$context[\'' . $value . '\']."';
                $result = preg_replace('#\(.+?\)#i', $replacement, $result, 1, $count);
            }

            // Компиляция итогового выражения
            $compiler
                ->addDebugInfo($this)
                ->write('echo "' . $result . '"')
                ->raw(";\n");
        }

    }