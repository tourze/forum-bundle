<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Enum\ThreadCommentState;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends AbstractCrudController<ThreadComment>
 */
#[AdminCrud(routePath: '/forum/thread-comment', routeName: 'forum_thread_comment')]
final class ForumThreadCommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadComment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子评论')
            ->setEntityLabelInPlural('帖子评论列表')
            ->setPageTitle('index', '帖子评论管理')
            ->setPageTitle('detail', '帖子评论详情')
            ->setPageTitle('edit', '编辑帖子评论')
            ->setPageTitle('new', '新建帖子评论')
            ->setHelp('index', '管理用户对帖子的评论，包括审核、查看和删除等操作')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'content'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);

        yield AssociationField::new('thread', '关联帖子')
            ->setFormTypeOption('choice_label', $this->getThreadChoiceLabel())
        ;

        yield AssociationField::new('user', '评论用户')
            ->setFormTypeOption('choice_label', $this->getUserChoiceLabel())
        ;

        yield AssociationField::new('replyUser', '回复用户')
            ->setFormTypeOption('choice_label', $this->getUserChoiceLabel())
            ->hideOnIndex()
        ;

        yield TextareaField::new('content', '评论内容')
            ->setRequired(true)
            ->setMaxLength(50)
            ->formatValue(function ($value): string {
                if (is_string($value) && '' !== $value) {
                    return mb_substr(strip_tags($value), 0, 50) . '...';
                }

                return '';
            })
        ;

        yield ChoiceField::new('status', '审核状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => ThreadCommentState::class])
            ->formatValue(function ($value) {
                return $value instanceof ThreadCommentState ? $value->getLabel() : '';
            })
            ->setRequired(true)
        ;

        yield TextField::new('parentId', '父级ID')
            ->hideOnIndex()
        ;

        yield TextField::new('rootParentId', '根父级ID')
            ->hideOnIndex()
        ;

        yield BooleanField::new('best', '是否最佳')
            ->setHelp('标记为最佳评论')
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
        $statusChoices = [];
        foreach (ThreadCommentState::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('content', '评论内容'))
            ->add(ChoiceFilter::new('status', '审核状态')->setChoices($statusChoices))
            ->add(BooleanFilter::new('best', '是否最佳'))
            ->add(EntityFilter::new('thread', '关联帖子'))
            ->add(EntityFilter::new('user', '评论用户'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    private function getUserChoiceLabel(): callable
    {
        return function (?UserInterface $user): string {
            if (null === $user) {
                return '';
            }
            if (method_exists($user, 'getUsername')) {
                $username = $user->getUsername();

                return is_string($username) ? $username : 'User';
            }

            return 'User';
        };
    }

    private function getThreadChoiceLabel(): callable
    {
        return function ($thread): string {
            if ($thread instanceof Thread) {
                return mb_substr($thread->getTitle() ?? '', 0, 50);
            }

            return '';
        };
    }
}
