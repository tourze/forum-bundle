<?php

namespace ForumBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use ForumBundle\Entity\ThreadCommentLike;

/**
 * @extends AbstractCrudController<ThreadCommentLike>
 */
#[AdminCrud]
final class ForumThreadCommentLikeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ThreadCommentLike::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('threadComment', '评论'),
            AssociationField::new('user', '用户'),
            ChoiceField::new('status', '点赞状态')
                ->setChoices([
                    '取消点赞' => 0,
                    '已点赞' => 1,
                ]),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('threadComment')
            ->add('user')
            ->add('status')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('评论点赞')
            ->setEntityLabelInPlural('评论点赞')
            ->setPageTitle('index', '评论点赞列表')
            ->setPageTitle('new', '创建评论点赞')
            ->setPageTitle('edit', '编辑评论点赞')
            ->setPageTitle('detail', '评论点赞详情')
        ;
    }
}
