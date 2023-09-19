<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09/07/18
 * Time: 11:35.
 */

namespace Wexample\SymfonyHelpers\Entity;

use App\Entity\Address;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use JetBrains\PhpStorm\Pure;
use Stringable;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Wexample\SymfonyHelpers\Entity\Traits\WithUserEntityTrait;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use function substr;

class Organization extends AbstractEntity implements Stringable
{
    use WithUserEntityTrait;

    #[ManyToOne(targetEntity: \App\Entity\OrganizationType::class)]
    #[JoinColumn(nullable: false)]
    protected OrganizationType $type;

    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: VariableHelper::VARIABLE_TYPE_STRING_LENGTH_DEFAULT)]
    #[Column(type: Types::STRING, length: 255)]
    protected string $title;

    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: VariableHelper::VARIABLE_TYPE_STRING_LENGTH_DEFAULT)]
    #[Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $website = null;

    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: VariableHelper::VARIABLE_TYPE_STRING_LENGTH_DEFAULT)]
    #[Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $companyIdentifier = null;

    #[ManyToOne(targetEntity: Address::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    protected ?Address $address = null;

    #[ManyToOne(targetEntity: \App\Entity\User::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    protected ?\App\Entity\User $user = null;

    /**
     * TODO Rename to phone.
     */
    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: 40)]
    #[Column(type: Types::STRING, length: 40, nullable: true)]
    protected ?string $number = null;

    #[Pure]
    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    public function getType(): ?OrganizationType
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(?OrganizationType $type)
    {
        $this->type = $type;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website)
    {
        $this->website = $website;
    }

    public function getCompanyIdentifier(bool $short = false)
    {
        if ($short) {
            return substr($this->companyIdentifier, 0, 9);
        }

        return $this->companyIdentifier;
    }

    public function setCompanyIdentifier(string $companyIdentifier)
    {
        $this->companyIdentifier = $companyIdentifier;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address)
    {
        $this->address = $address;
    }
}
