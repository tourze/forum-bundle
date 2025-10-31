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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Enum\ThreadType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends AbstractCrudController<Thread>
 */
#[AdminCrud(routePath: '/forum/thread', routeName: 'forum_thread')]
final class ForumThreadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Thread::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('帖子')
            ->setEntityLabelInPlural('帖子列表')
            ->setPageTitle('index', '帖子管理')
            ->setPageTitle('detail', '帖子详情')
            ->setPageTitle('edit', '编辑帖子')
            ->setPageTitle('new', '新建帖子')
            ->setHelp('index', '管理用户发布的帖子，包括审核、编辑和删除等操作')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'title', 'content', 'identify'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);

        yield TextField::new('title', '标题')
            ->setRequired(true)
            ->setMaxLength(200)
        ;

        yield AssociationField::new('user', '发布用户')
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

        yield ChoiceField::new('status', '审核状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => ThreadState::class])
            ->formatValue(function ($value) {
                return $value instanceof ThreadState ? $value->getLabel() : '';
            })
            ->setRequired(true)
        ;

        yield ChoiceField::new('type', '帖子类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => ThreadType::class])
            ->formatValue(function ($value) {
                return $value instanceof ThreadType ? $value->getLabel() : '';
            })
            ->setRequired(true)
        ;

        yield TextareaField::new('content', '内容')
            ->setRequired(true)
            ->hideOnIndex()
        ;

        yield ImageField::new('coverPicture', '封面图')
            ->setBasePath('/uploads/')
            ->setUploadDir('public/uploads/')
            ->hideOnForm()
        ;

        yield TextField::new('identify', '标识')
            ->setMaxLength(20)
            ->hideOnIndex()
        ;

        yield TextareaField::new('rejectReason', '驳回理由')
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield IntegerField::new('postId', '迁移数据ID')
            ->hideOnForm()
            ->hideOnIndex()
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
        foreach (ThreadState::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        $typeChoices = [];
        foreach (ThreadType::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('title', '标题'))
            ->add(TextFilter::new('content', '内容'))
            ->add(ChoiceFilter::new('status', '审核状态')->setChoices($statusChoices))
            ->add(ChoiceFilter::new('type', '帖子类型')->setChoices($typeChoices))
            ->add(EntityFilter::new('user', '发布用户'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
