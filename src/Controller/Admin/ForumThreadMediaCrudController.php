<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\ThreadMedia;

/**
 * @extends AbstractCrudController<ThreadMedia>
 */
#[AdminCrud]
final class ForumThreadMediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadMedia::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('thread', '帖子'),
            TextField::new('type', '媒体类型'),
            TextField::new('path', '来源路径'),
            TextField::new('thumbnail', '缩略图')
                ->hideOnIndex(),
            IntegerField::new('size', '大小')
                ->formatValue(function ($value): string {
                    if (is_int($value) || is_string($value)) {
                        return (string) $value . ' B';
                    }
                    return '0 B';
                }),
            TextField::new('options', '扩展选项')
                ->hideOnIndex(),
            AssociationField::new('createdBy', '创建人')
                ->hideOnForm(),
            AssociationField::new('updatedBy', '更新人')
                ->hideOnForm(),
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
            ->add(TextFilter::new('type', '媒体类型'))
            ->add(TextFilter::new('path', '来源路径'))
            ->add(EntityFilter::new('createdBy', '创建人'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子媒体')
            ->setEntityLabelInPlural('帖子媒体')
            ->setPageTitle('index', '帖子媒体列表')
            ->setPageTitle('new', '创建帖子媒体')
            ->setPageTitle('edit', '编辑帖子媒体')
            ->setPageTitle('detail', '帖子媒体详情')
        ;
    }
}
