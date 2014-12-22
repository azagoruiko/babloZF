<?php

namespace Bablo\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

class PasswordFunc extends FunctionNode {
    public function getSql(SqlWalker $sqlWalker) {
        $this->stringPrimary;
        
        return "password({$this->stringPrimary->dispatch($sqlWalker)})";
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->stringPrimary = $parser->StringExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
