<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class ArrayAnalyzer
{
    public function __construct(
        private TokenSkipper $tokenSkipper
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function getItemCount(Tokens $tokens, BlockInfo $blockInfo): int
    {
        $nextMeanninfulPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        if ($nextMeanninfulPosition === null) {
            return 0;
        }

        /** @var Token $nextMeaningfulToken */
        $nextMeaningfulToken = $tokens[$nextMeanninfulPosition];

        // no elements
        if ($this->isArrayCloser($nextMeaningfulToken)) {
            return 0;
        }

        $itemCount = 1;
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, static function (Token $token) use (
            &$itemCount
        ): void {
            if ($token->getContent() === ',') {
                ++$itemCount;
            }
        });

        return $itemCount;
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function isIndexedList(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        $isIndexedList = false;
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, static function (Token $token) use (
            &$isIndexedList
        ): void {
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $isIndexedList = true;
            }
        });

        return $isIndexedList;
    }

    /**
     * @param Tokens<Token> $tokens
     * @param callable(Token $token, int $i, Tokens $tokens): void $callable
     */
    public function traverseArrayWithoutNesting(Tokens $tokens, BlockInfo $blockInfo, callable $callable): void
    {
        for ($i = $blockInfo->getEnd() - 1; $i >= $blockInfo->getStart() + 1; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            /** @var Token $token */
            $token = $tokens[$i];
            $callable($token, $i, $tokens);
        }
    }

    private function isArrayCloser(Token $token): bool
    {
        if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return true;
        }

        return $token->getContent() === ')';
    }
}
