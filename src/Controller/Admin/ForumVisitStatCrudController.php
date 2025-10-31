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
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use ForumBundle\Entity\VisitStat;

/**
 * @extends AbstractCrudController<VisitStat>
 */
#[AdminCrud]
final class ForumVisitStatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisitStat::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('thread', '帖子'),
            IntegerField::new('likeTotal', '总点赞数'),
            IntegerField::new('shareTotal', '总分享数'),
            IntegerField::new('commentTotal', '总评论数'),
            IntegerField::new('visitTotal', '访问数'),
            IntegerField::new('collectCount', '收藏数'),
            IntegerField::new('likeRank', '点赞排行')
                ->hideOnIndex(),
            IntegerField::new('shareRank', '分享排行')
                ->hideOnIndex(),
            IntegerField::new('commentRank', '评论排行')
                ->hideOnIndex(),
            IntegerField::new('visitRank', '访问排行')
                ->hideOnIndex(),
            IntegerField::new('collectRank', '收藏排行')
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
            ->add(NumericFilter::new('likeTotal', '总点赞数'))
            ->add(NumericFilter::new('shareTotal', '总分享数'))
            ->add(NumericFilter::new('commentTotal', '总评论数'))
            ->add(NumericFilter::new('visitTotal', '访问数'))
            ->add(NumericFilter::new('collectCount', '收藏数'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('访问统计')
            ->setEntityLabelInPlural('访问统计')
            ->setPageTitle('index', '访问统计列表')
            ->setPageTitle('new', '创建访问统计')
            ->setPageTitle('edit', '编辑访问统计')
            ->setPageTitle('detail', '访问统计详情')
        ;
    }
}
