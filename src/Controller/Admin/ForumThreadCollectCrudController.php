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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends AbstractCrudController<ThreadCollect>
 */
#[AdminCrud(routePath: '/forum/thread-collect', routeName: 'forum_thread_collect')]
final class ForumThreadCollectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadCollect::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子收藏')
            ->setEntityLabelInPlural('帖子收藏列表')
            ->setPageTitle('index', '帖子收藏管理')
            ->setPageTitle('detail', '帖子收藏详情')
            ->setPageTitle('edit', '编辑帖子收藏')
            ->setPageTitle('new', '新建帖子收藏')
            ->setHelp('index', '管理用户对帖子的收藏记录，查看收藏统计')
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
                if ($thread instanceof Thread) {
                    return mb_substr($thread->getTitle() ?? '', 0, 50);
                }

                return '';
            })
        ;

        yield AssociationField::new('user', '收藏用户')
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

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('控制收藏记录是否有效，false表示已取消收藏')
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
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(EntityFilter::new('thread', '关联帖子'))
            ->add(EntityFilter::new('user', '收藏用户'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
