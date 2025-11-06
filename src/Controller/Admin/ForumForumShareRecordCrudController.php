<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\ForumShareRecord;

/**
 * @extends AbstractCrudController<ForumShareRecord>
 */
#[AdminCrud(routePath: '/forum/share-record', routeName: 'forum_share_record')]
final class ForumForumShareRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForumShareRecord::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID'),
            AssociationField::new('user', '用户'),
            TextField::new('type', '分享类型'),
            TextField::new('sourceId', '来源主键ID'),
            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', '用户'))
            ->add(TextFilter::new('type', '分享类型'))
            ->add(TextFilter::new('sourceId', '来源主键ID'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('分享记录')
            ->setEntityLabelInPlural('分享记录')
            ->setPageTitle('index', '分享记录列表')
            ->setPageTitle('new', '创建分享记录')
            ->setPageTitle('edit', '编辑分享记录')
            ->setPageTitle('detail', '分享记录详情')
        ;
    }
}
