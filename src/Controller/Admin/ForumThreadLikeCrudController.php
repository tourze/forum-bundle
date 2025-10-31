<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use ForumBundle\Entity\ThreadLike;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends AbstractCrudController<ThreadLike>
 */
#[AdminCrud(routePath: '/forum/thread-like', routeName: 'forum_thread_like')]
final class ForumThreadLikeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadLike::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子点赞')
            ->setEntityLabelInPlural('帖子点赞列表')
            ->setPageTitle('index', '帖子点赞管理')
            ->setPageTitle('detail', '帖子点赞详情')
            ->setPageTitle('edit', '编辑帖子点赞')
            ->setPageTitle('new', '新建帖子点赞')
            ->setHelp('index', '管理用户对帖子的点赞记录，查看点赞统计')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);

        yield AssociationField::new('thread', '关联帖子')
            ->setFormTypeOption('choice_label', function ($thread): string {
                if ($thread instanceof \ForumBundle\Entity\Thread) {
                    return mb_substr($thread->getTitle() ?? '', 0, 50);
                }
                return '';
            })
        ;

        yield AssociationField::new('user', '点赞用户')
            ->setFormTypeOption('choice_label', function (?UserInterface $user): string {
                if (null === $user) {
                    return '';
                }
                if (method_exists($user, 'getUsername')) {
                    $username = $user->getUsername();
                    return is_string($username) ? $username : 'User';
                }
                return 'User';
            })
        ;

        yield ChoiceField::new('status', '点赞状态')
            ->setChoices([
                '已点赞' => 1,
                '取消点赞' => 0,
            ])
            ->formatValue(function ($value) {
                return match ($value) {
                    1 => '已点赞',
                    0 => '取消点赞',
                    default => '未知',
                };
            })
            ->setRequired(true)
            ->setHelp('0表示取消点赞，1表示已点赞')
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
            ->add(ChoiceFilter::new('status', '点赞状态')->setChoices([
                '已点赞' => 1,
                '取消点赞' => 0,
            ]))
            ->add(EntityFilter::new('thread', '关联帖子'))
            ->add(EntityFilter::new('user', '点赞用户'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
