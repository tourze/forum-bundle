<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageActionType;
use ForumBundle\Enum\MessageType;

/**
 * @extends AbstractCrudController<MessageNotification>
 */
#[AdminCrud(routePath: '/forum/message-notification', routeName: 'forum_message_notification')]
final class ForumMessageNotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageNotification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID'),
            AssociationField::new('user', '接收用户'),
            AssociationField::new('sender', '发送者'),
            TextareaField::new('content', '消息内容'),
            ChoiceField::new('type', '通知类型')
                ->setChoices(array_combine(
                    array_map(fn (MessageType $case) => $case->value, MessageType::cases()),
                    MessageType::cases()
                )),
            ChoiceField::new('action', '操作类型')
                ->setChoices(array_combine(
                    array_map(fn (MessageActionType $case) => $case->value, MessageActionType::cases()),
                    MessageActionType::cases()
                ))
                ->setRequired(false),
            TextField::new('path', '跳转路径')->setRequired(false),
            TextField::new('pathType', '路径类型')->setRequired(false),
            TextField::new('targetId', '目标ID'),
            ChoiceField::new('readStatus', '已读状态')
                ->setChoices([
                    '未读' => 0,
                    '已读' => 1,
                ]),
            ChoiceField::new('deleted', '删除状态')
                ->setChoices([
                    '未删除' => 0,
                    '已删除' => 1,
                ]),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user')
            ->add('sender')
            ->add('type')
            ->add('action')
            ->add('readStatus')
            ->add('deleted')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息通知')
            ->setEntityLabelInPlural('消息通知')
            ->setPageTitle('index', '消息通知列表')
            ->setPageTitle('new', '创建消息通知')
            ->setPageTitle('edit', '编辑消息通知')
            ->setPageTitle('detail', '消息通知详情')
        ;
    }
}
