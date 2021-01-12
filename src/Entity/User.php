<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Exception\CantComputeAuthKey;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    const COMMON_USER = 1;
    const AUCTION_USER = 2;
    const MODERATOR = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $FIO = "";

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=41)
     */
    private $auth_key;

    /**
     * @ORM\Column(type="integer")
     */
    private $cash = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $security = self::COMMON_USER;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="user")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFIO(): ?string
    {
        return $this->FIO;
    }

    public function setFIO(?string $FIO): self
    {
        $this->FIO = $FIO;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday->format("Y-m-d");
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function setAuthKey(): self
    {
        $salt = getenv('SALT');
        if (isset($this->login) && isset($this->password)) {
            $this->auth_key = sha1($this->login . $salt) . ':' . sha1($this->password . $salt);
        } else {
            throw new CantComputeAuthKey('Ошибка подсчета AuthKey');
        }

        return $this;
    }

    public function getCash(): ?int
    {
        return $this->cash;
    }

    public function setCash(int $cash): self
    {
        $this->cash = $cash;

        return $this;
    }

    public function getSecurity(): ?int
    {
        return $this->security;
    }

    public function setSecurity(int $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function toArray(): array {
       return [
           'id' => $this->id,
           'login' => $this->login,
           'password' => $this->password,
           'auth_key' => $this->auth_key,
           'security' => $this->security,
           'FIO' => $this->FIO,
           'birthday' => $this->birthday
       ];
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }
}
