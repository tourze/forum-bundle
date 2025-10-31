<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\Dimension;

/**
 * @extends AbstractCrudController<Dimension>
 */
#[AdminCrud(routePath: '/forum/dimension', routeName: 'forum_dimension')]
final class ForumDimensionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dimension::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID'),
            TextField::new('title', '维度名'),
            TextField::new('code', '代号'),
            BooleanField::new('valid', '有效'),
            CollectionField::new('sortingRules', '排序规则')
                ->hideOnIndex(),
            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '维度名'))
            ->add(TextFilter::new('code', '代号'))
            ->add(BooleanFilter::new('valid', '有效'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('维度')
            ->setEntityLabelInPlural('维度')
            ->setPageTitle('index', '维度列表')
            ->setPageTitle('new', '创建维度')
            ->setPageTitle('edit', '编辑维度')
            ->setPageTitle('detail', '维度详情')
        ;
    }
}
