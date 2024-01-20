<?php declare(strict_types = 1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\php\node_visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * NodeVisitorFirstClass finds and stores the first Class_ node from the parse tree (abstract syntax tree).
 *
 * This class is designed to work in the context of the PhpParser (@see nikic/php-parser).
 *
 * Class NodeVisitorFirstClass
 */
class NodeVisitorFirstClass extends NodeVisitorAbstract
{
    /**
     * @var Class_|null
     */
    protected ?Class_ $classNode;

    /**
     * @function enterNode
     * @param Node $node
     * @return int|Node|void|null
     */
    public function enterNode($node)
    {
        if ($node instanceof Class_) {
            $this->classNode = $node;
            return NodeTraverser::STOP_TRAVERSAL;
        }
        return;
    }

    /**
     * @function getClassNode
     * @return Class_|null
     */
    public function getClassNode(): ?Class_
    {
        return $this->classNode ?? null;
    }
}
