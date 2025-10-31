<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\Channel;

/**
 * @extends AbstractCrudController<Channel>
 */
#[AdminCrud(routePath: '/forum/channel', routeName: 'forum_channel')]
final class ForumChannelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Channel::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('频道')
            ->setEntityLabelInPlural('频道列表')
            ->setPageTitle('index', '频道管理')
            ->setPageTitle('detail', '频道详情')
            ->setPageTitle('edit', '编辑频道')
            ->setPageTitle('new', '新建频道')
            ->setHelp('index', '管理帖子分类频道，控制频道的有效性和显示')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'title'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);

        yield TextField::new('title', '标题')
            ->setRequired(true)
            ->setMaxLength(50)
        ;

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('控制频道是否在前端显示')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '标题'))
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
