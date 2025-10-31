<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\SortingRule;

/**
 * @extends AbstractCrudController<SortingRule>
 */
#[AdminCrud]
final class ForumSortingRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SortingRule::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title', '规则名'),
            AssociationField::new('dimension', '维度'),
            TextareaField::new('formula', '规则公式'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '规则名'))
            ->add('dimension')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('排序规则')
            ->setEntityLabelInPlural('排序规则')
            ->setPageTitle('index', '排序规则列表')
            ->setPageTitle('new', '创建排序规则')
            ->setPageTitle('edit', '编辑排序规则')
            ->setPageTitle('detail', '排序规则详情')
        ;
    }
}
