<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class PrettyPrinterAbstract
{
    protected $precedenceMap = [
        // [precedence, associativity] where for the latter -1 is %left, 0 is %nonassoc and 1 is %right
        'Expr_BinaryOp_Pow'            => [0,  1],
        'Expr_BitwiseNot'              => [10,  1],
        'Expr_PreInc'                  => [10,  1],
        'Expr_PreDec'                  => [10,  1],
        'Expr_PostInc'                 => [10, -1],
        'Expr_PostDec'                 => [10, -1],
        'Expr_UnaryPlus'               => [10,  1],
        'Expr_UnaryMinus'              => [10,  1],
        'Expr_Cast_Int'                => [10,  1],
        'Expr_Cast_Double'             => [10,  1],
        'Expr_Cast_String'             => [10,  1],
        'Expr_Cast_Array'              => [10,  1],
        'Expr_Cast_Object'             => [10,  1],
        'Expr_Cast_Bool'               => [10,  1],
        'Expr_Cast_Unset'              => [10,  1],
        'Expr_ErrorSuppress'           => [10,  1],
        'Expr_Instanceof'              => [20,  0],
        'Expr_BooleanNot'              => [30,  1],
        'Expr_BinaryOp_Mul'            => [40, -1],
        'Expr_BinaryOp_Div'            => [40, -1],
        'Expr_BinaryOp_Mod'            => [40, -1],
        'Expr_BinaryOp_Plus'           => [50, -1],
        'Expr_BinaryOp_Minus'          => [50, -1],
        'Expr_BinaryOp_Concat'         => [50, -1],
        'Expr_BinaryOp_ShiftLeft'      => [60, -1],
        'Expr_BinaryOp_ShiftRight'     => [60, -1],
        'Expr_BinaryOp_Smaller'        => [70,  0],
        'Expr_BinaryOp_SmallerOrEqual' => [70,  0],
        'Expr_BinaryOp_Greater'        => [70,  0],
        'Expr_BinaryOp_GreaterOrEqual' => [70,  0],
        'Expr_BinaryOp_Equal'          => [80,  0],
        'Expr_BinaryOp_NotEqual'       => [80,  0],
        'Expr_BinaryOp_Identical'      => [80,  0],
        'Expr_BinaryOp_NotIdentical'   => [80,  0],
        'Expr_BinaryOp_Spaceship'      => [80,  0],
        'Expr_BinaryOp_BitwiseAnd'     => [90, -1],
        'Expr_BinaryOp_BitwiseXor'     => [100, -1],
        'Expr_BinaryOp_BitwiseOr'      => [110, -1],
        'Expr_BinaryOp_BooleanAnd'     => [120, -1],
        'Expr_BinaryOp_BooleanOr'      => [130, -1],
        'Expr_BinaryOp_Coalesce'       => [140,  1],
        'Expr_Ternary'                 => [150, -1],
        // parser uses %left for assignments, but they really behave as %right
        'Expr_Assign'                  => [160,  1],
        'Expr_AssignRef'               => [160,  1],
        'Expr_AssignOp_Plus'           => [160,  1],
        'Expr_AssignOp_Minus'          => [160,  1],
        'Expr_AssignOp_Mul'            => [160,  1],
        'Expr_AssignOp_Div'            => [160,  1],
        'Expr_AssignOp_Concat'         => [160,  1],
        'Expr_AssignOp_Mod'            => [160,  1],
        'Expr_AssignOp_BitwiseAnd'     => [160,  1],
        'Expr_AssignOp_BitwiseOr'      => [160,  1],
        'Expr_AssignOp_BitwiseXor'     => [160,  1],
        'Expr_AssignOp_ShiftLeft'      => [160,  1],
        'Expr_AssignOp_ShiftRight'     => [160,  1],
        'Expr_AssignOp_Pow'            => [160,  1],
        'Expr_YieldFrom'               => [165,  1],
        'Expr_Print'                   => [168,  1],
        'Expr_BinaryOp_LogicalAnd'     => [170, -1],
        'Expr_BinaryOp_LogicalXor'     => [180, -1],
        'Expr_BinaryOp_LogicalOr'      => [190, -1],
        'Expr_Include'                 => [200, -1],
    ];

    protected $noIndentToken;
    protected $docStringEndToken;
    protected $canUseSemicolonNamespaces;
    protected $options;

    /**
     * Creates a pretty printer instance using the given options.
     *
     * Supported options:
     *  * bool $shortArraySyntax = false: Whether to use [] instead of array() as the default array
     *                                    syntax, if the node does not specify a format.
     *
     * @param array $options Dictionary of formatting options
     */
    public function __construct(array $options = [])
    {
        $this->noIndentToken     = '_NO_INDENT_' . mt_rand();
        $this->docStringEndToken = '_DOC_STRING_END_' . mt_rand();

        $defaultOptions = ['shortArraySyntax' => false];
        $this->options  = $options + $defaultOptions;
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts)
    {
        $this->preprocessNodes($stmts);

        return ltrim($this->handleMagicTokens($this->pStmts($stmts, false)));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node)
    {
        return $this->handleMagicTokens($this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts)
    {
        if (! $stmts) {
            return "<?php\n\n";
        }

        $p = "<?php\n\n" . $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        return $p;
    }

    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes)
    {
        /* We can use semicolon-namespaces unless there is a global namespace declaration */
        $this->canUseSemicolonNamespaces = true;
        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_ && $node->name === null) {
                $this->canUseSemicolonNamespaces = false;
            }
        }
    }

    protected function handleMagicTokens($str)
    {
        // Drop no-indent tokens
        $str = str_replace($this->noIndentToken, '', $str);

        // Replace doc-string-end tokens with nothing or a newline
        $str = str_replace($this->docStringEndToken . ";\n", ";\n", $str);
        return str_replace($this->docStringEndToken, "\n", $str);
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param Node[] $nodes Array of nodes
     * @param bool $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true)
    {
        $result = '';
        foreach ($nodes as $node) {
            $comments = $node->getAttribute('comments', []);
            if ($comments) {
                $result .= "\n" . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $result .= "\n" . $this->p($node) . ($node instanceof Expr ? ';' : '');
        }

        if ($indent) {
            return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n    ", $result);
        }
        return $result;
    }

    /**
     * Pretty prints a node.
     *
     * @param Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(Node $node)
    {
        return $this->{'p' . $node->getType()}($node);
    }

    protected function pInfixOp($type, Node $leftNode, $operatorString, Node $rightNode)
    {
        [$precedence, $associativity] = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
             . $operatorString
             . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }

    protected function pPrefixOp($type, $operatorString, Node $node)
    {
        [$precedence, $associativity] = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }

    protected function pPostfixOp($type, Node $node, $operatorString)
    {
        [$precedence, $associativity] = $this->precedenceMap[$type];
        return $this->pPrec($node, $precedence, $associativity, -1) . $operatorString;
    }

    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param Node $node Node to pretty print
     * @param int $parentPrecedence Precedence of the parent operator
     * @param int $parentAssociativity Associativity of parent operator
     *                                 (-1 is left, 0 is nonassoc, 1 is right)
     * @param int $childPosition Position of the node relative to the operator
     *                           (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    protected function pPrec(Node $node, $parentPrecedence, $parentAssociativity, $childPosition)
    {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->p($node) . ')';
            }
        }

        return $this->p($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param string $glue Character to implode with
     *
     * @return string Imploded pretty printed nodes
     */
    protected function pImplode(array $nodes, $glue = '')
    {
        $pNodes = [];
        foreach ($nodes as $node) {
            if ($node === null) {
                $pNodes[] = '';
            } else {
                $pNodes[] = $this->p($node);
            }
        }

        return implode($glue, $pNodes);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes)
    {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Pretty prints a comma-separated list of nodes in multiline style, including comments.
     *
     * The result includes a leading newline and one level of indentation (same as pStmts).
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param bool $trailingComma Whether to use a trailing comma
     *
     * @return string Comma separated pretty printed nodes in multiline style
     */
    protected function pCommaSeparatedMultiline(array $nodes, $trailingComma)
    {
        $result  = '';
        $lastIdx = count($nodes) - 1;
        foreach ($nodes as $idx => $node) {
            if ($node !== null) {
                $comments = $node->getAttribute('comments', []);
                if ($comments) {
                    $result .= "\n" . $this->pComments($comments);
                }

                $result .= "\n" . $this->p($node);
            } else {
                $result .= "\n";
            }
            if ($trailingComma || $idx !== $lastIdx) {
                $result .= ',';
            }
        }

        return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n    ", $result);
    }

    /**
     * Signals the pretty printer that a string shall not be indented.
     *
     * @param string $string Not to be indented string
     *
     * @return string string marked with $this->noIndentToken's
     */
    protected function pNoIndent($string)
    {
        return str_replace("\n", "\n" . $this->noIndentToken, $string);
    }

    /**
     * Prints reformatted text of the passed comments.
     *
     * @param Comment[] $comments List of comments
     *
     * @return string Reformatted text of comments
     */
    protected function pComments(array $comments)
    {
        $formattedComments = [];

        foreach ($comments as $comment) {
            $formattedComments[] = $comment->getReformattedText();
        }

        return implode("\n", $formattedComments);
    }
}
