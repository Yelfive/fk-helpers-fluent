<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 */

/**
 * Generate annotation for the fluent classes
 */

spl_autoload_register(function ($class) {
    $path = dirname(__DIR__) . "/src/" . str_replace('\\', '/', str_replace('fk\fluent\\', '', $class)) . '.php';
    include_once $path;
});

/**
 * @param string $relative_to_root
 * @return string
 * @throws ReflectionException
 */
function annotate($relative_to_root)
{
    $className = 'fk\fluent\\' . str_replace('/', '\\', preg_replace(['#^src/#', '#\.php$#'], '', $relative_to_root));
    $rc = new ReflectionClass($className);
    $definitions = [];
    foreach ($rc->getMethods() as $method) {
        if (!preg_match('#^fluent(\w+)#', $method->getName(), $matches)) continue;
        $methodName = lcfirst($matches[1]);
        [$description, $args] = getParametersFromComments($method);
        if ($description) $description = " $description";
        $definitions[] = ' * @method ' . $rc->getShortName() . " $methodName(" . implode(', ', $args) . ")$description";
    }
    return implode("\n", $definitions);
}

function wrapScalar($input)
{
    if (is_numeric($input)) {
        return $input;
    } else if (is_string($input)) {
        return "'$input'";
    } else if (is_null($input)) {
        return 'null';
    } else if (is_bool($input)) {
        return $input ? 'true' : 'false';
    } else if (is_int($input)) {
        return $input;
    }
}

function getParametersFromComments(ReflectionMethod $method)
{
    $comments = $method->getDocComment();
    $meta = [];
    foreach ($method->getParameters() as $p) {
        $name = '$' . $p->name;
        if ($p->isVariadic()) $name = "...$name";

        $type = $p->getType();
        $paramPattern = "#\*\s*@param\s+([^$]+)\s+" . preg_quote($name, '#') . "#";

        if (!$type && preg_match($paramPattern, $comments, $matches)) {
            $type = $matches[1];
        }
        $meta[$name] = [$type, $p->isDefaultValueAvailable() ? wrapScalar($p->getDefaultValue()) : false];
    }

    $args = [];
    foreach ($meta as $name => [$type, $default]) {
        $args[] = "$type $name" . ($default ? " = $default" : '');
    }

    $description = '';
    foreach (explode("\n", $comments) as $k => $comment) {
        if (preg_match('#\s*\*\s*@\w+#', $comment)) break;
        $description .= preg_replace('#^/?\s*\*+\s*#', '', $comment) . ' ';
    }
    return [rtrim($description), $args];
}

$files = $argv;
array_shift($files);

foreach ($files as $file) {
    fwrite(STDERR, "\033[32m$file\033[0m\n");
    fwrite(STDOUT, "\n" . annotate($file));
    fwrite(STDERR, "\n");
}