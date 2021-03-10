<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductosRepository::class)
 */
class Productos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_externo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Marcas::class, inversedBy="productos")
     */
    private $marca;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="smallint")
     */
    private $habilitado;

    /**
     * @ORM\Column(type="smallint")
     */
    private $eliminado = 0;

    /**
     * @ORM\OneToMany(targetEntity=ProductosCategorias::class, mappedBy="producto")
     */
    private $productosCategorias;

    /**
     * @ORM\OneToMany(targetEntity=ProductosCaracteristicas::class, mappedBy="producto")
     */
    private $productosCaracteristicas;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $precio;

    public function __construct()
    {
        $this->productosCategorias = new ArrayCollection();
        $this->productosCaracteristicas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdExterno(): ?int
    {
        return $this->id_externo;
    }

    public function setIdExterno(?int $id_externo): self
    {
        $this->id_externo = $id_externo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getMarca(): ?Marcas
    {
        return $this->marca;
    }

    public function setMarca(?Marcas $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getHabilitado(): ?int
    {
        return $this->habilitado;
    }

    public function setHabilitado(int $habilitado): self
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    public function getEliminado(): ?int
    {
        return $this->eliminado;
    }

    public function setEliminado(int $eliminado): self
    {
        $this->eliminado = $eliminado;

        return $this;
    }

    /**
     * @return Collection|ProductosCategorias[]
     */
    public function getProductosCategorias(): Collection
    {
        return $this->productosCategorias;
    }

    public function addProductosCategoria(ProductosCategorias $productosCategoria): self
    {
        if (!$this->productosCategorias->contains($productosCategoria)) {
            $this->productosCategorias[] = $productosCategoria;
            $productosCategoria->setProducto($this);
        }

        return $this;
    }

    public function removeProductosCategoria(ProductosCategorias $productosCategoria): self
    {
        if ($this->productosCategorias->removeElement($productosCategoria)) {
            // set the owning side to null (unless already changed)
            if ($productosCategoria->getProducto() === $this) {
                $productosCategoria->setProducto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductosCaracteristicas[]
     */
    public function getProductosCaracteristicas(): Collection
    {
        return $this->productosCaracteristicas;
    }

    public function addProductosCaracteristica(ProductosCaracteristicas $productosCaracteristica): self
    {
        if (!$this->productosCaracteristicas->contains($productosCaracteristica)) {
            $this->productosCaracteristicas[] = $productosCaracteristica;
            $productosCaracteristica->setProducto($this);
        }

        return $this;
    }

    public function removeProductosCaracteristica(ProductosCaracteristicas $productosCaracteristica): self
    {
        if ($this->productosCaracteristicas->removeElement($productosCaracteristica)) {
            // set the owning side to null (unless already changed)
            if ($productosCaracteristica->getProducto() === $this) {
                $productosCaracteristica->setProducto(null);
            }
        }

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(?float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }
}
