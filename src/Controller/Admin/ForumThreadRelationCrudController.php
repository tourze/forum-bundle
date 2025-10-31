<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\ThreadRelation;
use ForumBundle\Enum\ThreadRelationType;

/**
 * @extends AbstractCrudController<ThreadRelation>
 */
#[AdminCrud]
final class ForumThreadRelationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadRelation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('sourceId', '来源ID'),
            ChoiceField::new('sourceType', '来源类型')
                ->setChoices([
                    '文章' => ThreadRelationType::CMS_ENTITY,
                ])
                ->renderExpanded()
                ->allowMultipleChoices(false),
            AssociationField::new('thread', '帖子'),
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
            ->add(TextFilter::new('sourceId', '来源ID'))
            ->add(ChoiceFilter::new('sourceType', '来源类型')
                ->setChoices([
                    '文章' => ThreadRelationType::CMS_ENTITY,
                ]))
            ->add(EntityFilter::new('thread', '帖子'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子关系')
            ->setEntityLabelInPlural('帖子关系')
            ->setPageTitle('index', '帖子关系列表')
            ->setPageTitle('new', '创建帖子关系')
            ->setPageTitle('edit', '编辑帖子关系')
            ->setPageTitle('detail', '帖子关系详情')
        ;
    }
}
