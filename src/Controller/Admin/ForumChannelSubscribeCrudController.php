<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use ForumBundle\Entity\ChannelSubscribe;

/**
 * @extends AbstractCrudController<ChannelSubscribe>
 */
#[AdminCrud(routePath: '/forum/channel-subscribe', routeName: 'forum_channel_subscribe')]
final class ForumChannelSubscribeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChannelSubscribe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID'),
            AssociationField::new('user', '用户'),
            AssociationField::new('channel', '频道'),
            BooleanField::new('valid', '有效'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user')
            ->add('channel')
            ->add('valid')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('频道订阅')
            ->setEntityLabelInPlural('频道订阅')
            ->setPageTitle('index', '频道订阅列表')
            ->setPageTitle('new', '创建频道订阅')
            ->setPageTitle('edit', '编辑频道订阅')
            ->setPageTitle('detail', '频道订阅详情')
        ;
    }
}
