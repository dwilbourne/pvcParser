<?php declare(strict_types = 1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\php\node_visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * NodeVisitorNamespace finds the namespace declaration in a php file and stores it.
 *
 * Class NodeVisitorNamespace
 */
class NodeVisitorNamespace extends NodeVisitorAbstract
{
    /**
     * @var Namespace_
     */
    protected Namespace_ $namespace;

    /**
     * @function enterNode
     * @param Node $node
     * @return int|Node|void|null
     */
    public function enterNode($node)
    {
        if ($node instanceof Namespace_) {
            $this->namespace = $node;
            return NodeTraverser::STOP_TRAVERSAL;
        }
        return;
    }

    /**
     * @function getNamespace
     * @return Namespace_
     */
    public function getNamespace(): Namespace_
    {
        return $this->namespace;
    }
}
