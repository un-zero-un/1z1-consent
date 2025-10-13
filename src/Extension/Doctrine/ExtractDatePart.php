<?php

namespace App\Extension\Doctrine;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * ExtractDatePart ::= "EXTRACT_DATE_PART" "(" ArithmeticPrimary "," StringPrimary ")"
 */
class ExtractDatePart extends FunctionNode
{
    public const string YEAR = 'year';
    public const string MONTH = 'month';
    public const string DAY = 'day';

    public const array PARTS = [
        self::YEAR,
        self::MONTH,
        self::DAY,
    ];

    private ?Node $dateExpression = null;
    private ?Node $part = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->part = $parser->StringPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        assert($this->dateExpression instanceof Node);
        assert($this->part instanceof Node);

        if (!in_array($this->part->value, self::PARTS, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid date part "%s" in EXTRACT_DATE_PART function. Allowed parts are: %s', $this->part->value, implode(', ', self::PARTS)));
        }

        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if ($platform instanceof AbstractMySQLPlatform || $platform instanceof PostgreSQLPlatform) {
            return sprintf('EXTRACT(%s FROM %s)', strtoupper($this->part->value), $this->dateExpression->dispatch($sqlWalker));
        }

        if ($platform instanceof SQLitePlatform) {
            return sprintf(
                'SUBSTRING(%s, %d, %d)',
                $this->dateExpression->dispatch($sqlWalker),
                match ($this->part->value) {
                    self::YEAR => 0,
                    self::MONTH => 5,
                    self::DAY => 8,
                },
                match ($this->part->value) {
                    self::YEAR => 4,
                    default => 2,
                },
            );
        }

        throw new \RuntimeException(sprintf(
            'The EXTRACT_DATE_PART function is not supported by the "%s" database platform.',
            get_class($platform),
        ));
    }
}
