<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCRUDControllerCommand extends Command
{
    const NAME = 'alpha:generate:crud-controller';
    const CONTROLLER_TEMPLATE = <<<EOF
<?php

namespace <namespace>;

use <entity_fqcn>;
use AppBundle\Handler\ObjectHandler;
use FOS\RestBundle\Controller\Annotations\Route;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/<plural_name>")
 */
class <singular_name_capitalized>CRUDController extends AbstractObjectCRUDController
{
    /**
     * @DI\InjectParams({
     *     "handler"=@DI\Inject("handler.<singular_name>"),
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     *
     * @param ObjectHandler \$handler
     * @param Serializer \$serializer
     */
    public function __construct(ObjectHandler \$handler, Serializer \$serializer)
    {
        parent::__construct('<entity_class>', \$handler, \$serializer);
    }

    /**
     * @ApiDoc(
     *     description="Create a New <singular_name_capitalized>",
     *     section="<singular_name_capitalized>",
     *     statusCodes={
     *         201="<singular_name_capitalized> was successfully created and persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error persisting the <singular_name_capitalized>"
     *     }
     * )
     *
     * @Route("", name="api_create_<singular_name>")
     * @Method({"POST"})
     *
     * @param Request \$request
     *
     * @return Response
     */
    public function create<singular_name_capitalized>(Request \$request)
    {
       return parent::createObject(\$request);
    }

    /**
     * @ApiDoc(
     *     description="Deletes an Existing <singular_name_capitalized>",
     *     section="<singular_name_capitalized>",
     *     statusCodes={
     *         204="<singular_name_capitalized> was successfully deleted",
     *         404="<singular_name_capitalized> with given UUID not found",
     *         500="Server encountered an error deleting the <singular_name_capitalized>"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_delete_<singular_name>}}")
     * @Method({"DELETE"})
     *
     * @ParamConverter(
     *     "<singular_name>",
     *     class="<entity_class>",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param <entity_class> \$<singular_name>
     *
     * @return Response
     */
    public function delete<singular_name_capitalized>(<entity_class> \$<singular_name>)
    {
        return parent::deleteObject(\$<singular_name>);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing <singular_name_capitalized>",
     *     section="<singular_name_capitalized>",
     *     statusCodes={
     *         201="<singular_name_capitalized> was successfully modified and changes were persisted",
     *         400="Invalid <singular_name_capitalized> Data or Bad UUID",
     *         404="<singular_name_capitalized> with given UUID not found.",
     *         500="Server encountered an error saving the <singular_name_capitalized> changes"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_edit_<singular_name>")
     * @Method({"PUT"})
     *
     * @ParamConverter(
     *     "<singular_name>",
     *     class="<entity_class>",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request \$request
     * @param <entity_class> \$<singular_name>
     *
     * @return Response
     */
    public function edit<singular_name_capitalized>(Request \$request, <entity_class> \$<singular_name>)
    {
        return parent::editObject(\$request, \$<singular_name>);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a <singular_name_capitalized> by UUID",
     *     section="<singular_name_capitalized>",
     *     statusCodes={
     *         200="<singular_name_capitalized> with given UUID found",
     *         400="Invalid UUID provided",
     *         404="<singular_name_capitalized> was not found given the UUID"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_get_<singular_name>")
     * @Method({"GET"})
     *
     * @ParamConverter(
     *     "<singular_name>",
     *     class="<entity_class>",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request \$request
     * @param <entity_class> \$<singular_name>
     *
     * @return Response
     */
    public function get<singular_name_capitalized>(Request \$request, <entity_class> \$<singular_name>)
    {
        return parent::getObject(\$request, \$<singular_name>);
    }

    /**
     * @ApiDoc(
     *     description="Get List of <plural_name_capitalized>",
     *     section="<singular_name_capitalized>",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     statusCodes={
     *          200="<singular_name_capitalized> List was retrieved.",
     *          204="No <plural_name_capitalized> were found and no content was returned."
     *     }
     * )
     *
     * @Route("", name="api_list_<singular_name>")
     * @Method({"GET"})
     *
     * @param Request \$request
     *
     * @return Response
     */
    public function get<singular_name_capitalized>List(Request \$request)
    {
        return parent::getObjectList(\$request);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Partially Update a <singular_name_capitalized> by UUID",
     *     section="<singular_name_capitalized>",
     *     statusCodes={
     *         200="<singular_name_capitalized> was successfully updated and changes were persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error updating <singular_name_capitalized> or persisting changes"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_partial_update_<singular_name>")
     * @Method({"PATCH"})
     *
     * @ParamConverter(
     *     "<singular_name>",
     *     class="<entity_class>",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request \$request
     * @param <singular_name_capitalized> \$<singular_name>
     *
     * @return Response
     */
    public function partialUpdate<singular_name_capitalized>(Request \$request, <singular_name_capitalized> \$<singular_name>)
    {
        return parent::updateObject(\$request, \$<singular_name>);
    }
}

EOF;

    public function __construct()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Generate a CRUD Controller');

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $placeholders = [
            '<entity_class>',
            '<entity_fqcn>',
            '<namespace>',
            '<plural_name>',
            '<plural_name_capitalized>',
            '<singular_name>',
            '<singular_name_capitalized>',
        ];

        $destinationPath = $style->ask('Enter Destination Path');
        $entityFQCN = $style->ask('Enter the FQCN Entity Class');
        $namespace = $style->ask('Enter the Controller Namespace');
        $singularName = $style->ask('Enter the Entity\'s Singular Name');
        $pluralName = $style->ask('Enter the Entity\'s Plural Name');

        $replacements = [
            $this->getEntityClassFromFQCN($entityFQCN),
            $entityFQCN,
            $namespace,
            lcfirst($pluralName),
            ucfirst($pluralName),
            lcfirst($singularName),
            ucfirst($singularName),
        ];

        $rendered = str_replace($placeholders, $replacements, self::CONTROLLER_TEMPLATE);

        $path = $destinationPath . '/' . ucfirst($singularName) . 'CRUDController.php';

        if (false === file_put_contents($path, $rendered)) {
            $style->error('Error writing to \'' . $path . '\'');

            return -1;
        }

        chmod($path, 0664);

        return 0;
    }

    /**
     * @param string $fqcn
     *
     * @return string
     */
    private function getEntityClassFromFQCN($fqcn)
    {
        $pieces = explode('\\', $fqcn);

        return array_pop($pieces);
    }
}
