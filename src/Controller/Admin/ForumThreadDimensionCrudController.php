<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use ForumBundle\Entity\ThreadDimension;

/**
 * @extends AbstractCrudController<ThreadDimension>
 */
#[AdminCrud]
final class ForumThreadDimensionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadDimension::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('thread', '帖子'),
            AssociationField::new('dimension', '维度'),
            IntegerField::new('value', '维度数值'),
            ArrayField::new('context', '关联数据')
                ->hideOnIndex(),
            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
            DateTimeField::new('updateTime', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('thread', '帖子'))
            ->add(EntityFilter::new('dimension', '维度'))
            ->add(NumericFilter::new('value', '维度数值'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子维度')
            ->setEntityLabelInPlural('帖子维度')
            ->setPageTitle('index', '帖子维度列表')
            ->setPageTitle('new', '创建帖子维度')
            ->setPageTitle('edit', '编辑帖子维度')
            ->setPageTitle('detail', '帖子维度详情')
        ;
    }
}
