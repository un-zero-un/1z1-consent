<?php

declare(strict_types=1);

namespace App\Admin\Filter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ConsentStatusFilter implements FilterInterface
{
    use FilterTrait;

    #[\Override]
    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $value = $filterDataDto->getValue();
        if (!$value) {
            return;
        }

        if (!in_array($value, ['accepted', 'declined'])) {
            return;
        }

        if ('accepted' === $value) {
            $queryBuilder
                ->innerJoin('entity.trackerConsents', 'tc')
                ->andWhere('tc.accepted = TRUE');

            return;
        }

        $queryBuilder
            ->leftJoin('entity.trackerConsents', 'tc', Join::WITH, 'tc.accepted = TRUE')
            ->andWhere('tc.id IS NULL');
    }

    public static function new(string $propertyName, ?string $label = null): self
    {
        return new self()
            ->setFilterFqcn(self::class)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(ChoiceType::class)
            ->setFormTypeOption('choices', ['Accepté (y compris partiellement)' => 'accepted', 'Refusé (y compris inconnu)' => 'declined'])
            ->setFormTypeOption('placeholder', ' ');
    }
}
