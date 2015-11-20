<?php
/*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
* A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
* OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
* LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
* DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
* THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
* OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
* This software consists of voluntary contributions made by many individuals
* and is licensed under the MIT license. For more information, see
* <http://www.doctrine-project.org>.
*/

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
* Type that maps a PHP array to a clob SQL type.
*
* @since 2.0
*/
class EnumType extends Type
{
    protected $default;
    protected $name = 'enum';
    protected $values = array();

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // TODO: rozkminić domyślne wartości w enumie, na razie niewspierane
        if(count($this->values) == 0) $this->values = $fieldDeclaration['values'];

        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, $this->values);

        $vals = implode(', ', $values);

        $decl = ($this->default !== null)
        ? sprintf("ENUM(%s) DEFAULT '%s'",
            $vals,
            $this->default
        )
        : sprintf("ENUM(%s)",
            $vals
        );
        return $decl;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null and !in_array($value, $this->values)) {
            $msg = sprintf('Value "%s" is not a proper %s value.', $value, $this->name);
            throw new \InvalidArgumentException($msg);
        }
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }
}
