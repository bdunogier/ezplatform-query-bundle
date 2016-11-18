<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace BD\EzPlatformQueryBundle\Formatter\SearchHit;

use BD\EzPlatformQueryBundle\Exception\InvalidTypeException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Formats a search hit using an expression.
 *
 * The SearchHit is available as 'hit'.
 * A 'content_info' function will return the ContentInfo from the SearchHit.
 */
class ExpressionLanguageFormatter implements SearchHitFormatter
{
    /**
     * The expression that will be evaluated to format the search hit. Must not start with '@='.
     * @var string
     */
    private $expression;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    function __construct($expression, Repository $repository)
    {
        $this->expression = $expression;
        $this->repository = $repository;
    }

    public function format(SearchHit $searchHit)
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->addFunction(
            new ExpressionFunction('content_info', [$this, 'failCompilation'], [$this, 'getContentInfo'])
        );
        $expressionLanguage->addFunction(
            new ExpressionFunction('type', [$this, 'failCompilation'], [$this, 'getContentType'])
        );

        return $expressionLanguage->evaluate($this->expression, ['hit' => $searchHit]);
    }

    /**
     * Returns the ContentInfo from a ValueObject.
     *
     * @param array $context
     * @param Values\ValueObject $valueObject
     * @return Values\Content\ContentInfo
     * @throws \Exception
     */
    public function getContentInfo(array $context, SearchHit $searchHit)
    {
        $valueObject = $searchHit->valueObject;

        if ($valueObject instanceof Values\Content\Location) {
            return $valueObject->contentInfo;
        } elseif ($valueObject instanceof Values\Content\Content || $valueObject instanceof Values\Content\ContentInfo) {
            return $valueObject;
        } else {
            throw new InvalidTypeException(
                'ValueObject',
                $valueObject,
                'eZ\Publish\API\Repository\Values\Content\(Location,ContentInfo,Content)'
            );
        }
    }

    /**
     * @param Values\Content\Search\SearchHit $searchHit
     * @return Values\ContentType\ContentType
     */
    public function getContentType(array $context, SearchHit $searchHit)
    {
        return $this->repository->getContentTypeService()
            ->loadContentType($this->getContentInfo([], $searchHit)->contentTypeId);

    }

    public function failCompilation()
    {
        throw new \Exception('This expression can\'t be compiled.');
    }
}
