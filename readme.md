# EntityToExcel
This package adds a command to your symfony project. You'll be able to export excel files representing your entities in order to help you to fill database with client data.  
This program doesn't export any data. It just export structure of your data inb order to help you and/or your clients to give you data well formatted before import them.

# Requirements
Symfony > 4.4  
php > 7.0 

# Read before
This software is free to use, modify and share. It's made with a lot of love but comes without any guarantee.

# Install
 first run
 `composer require frvaillant/entitytoexcel`  
 
 Edit your `config/services.yaml` file and add under services key :
 ```yaml
services:
    EntityToExcel\Command\EntityToCsvCommand:
            tags:
                - { name: 'console.command', command: 'entity:excel' }
```

# run
launch command in your terminal :  
`php bin/console entity:excel Entity`  
Replace Entity by the name of the entity you want to export as excel file

If you get an error like  
 `Attempted to load class "Spreadsheet" from namespace "PhpOffice\PhpSpreadsheet".  
    Did you forget a "use" statement for another namespace? `  
then run `composer require phpoffice/phpspreadsheet`  
 
The file is exported as `Entity.xlsx` in the public/xls directory

# Excel file description
Each property of your entity is reported on each column of the table.  
On the first line, the name of the property  
On the second line, the display name (if you choose some (see below))  
On the third line, the default values

# Get more
The package comes with an annotation class wich allows you to be more precise  
Use it with `@EtEx()`  

- **exclude field**  
If you want to exclude one of your entity properties from your excel file :  
```php
    /**
     * @EtEx(exclude=true)
     * @ORM\Column(type="string", length=255)
     */
    private $name;
```

- **Add a dropdown value selector**  
if you want to add choices to fill cells, you can add a list parameter as below  
```php
    /**
     * @EtEx(list={"1", "0"}, defaultValue="0")
     * @ORM\Column(type="boolean")
     */
    private $isMusician;
```

- **Add a dropdown selector from another entity**  
If your property is for example a ManyToOne Relation with another entity, you can list the values from your entity usin the listFromEntityWith argument.  
This argument needs the name of the field you want to use to list your linked entity.  
```php
     /**
     * @EtEx(listFromEntityWith="name")
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="companies")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;
```

- **Default value**  
You can add a default value. This value will fill the cell on the third line as an example.  
```php
    /**
     * @EtEx(defaultValue="My value")
     * @ORM\Column(type="string")
     */
    private $label;
```

- **display name**  
By default, the title of each column is the name of the property in the entity.  
But sometimes, you'll probably want to display a translation of the terms used in entity definition.  
To do this, you can add a displayName paramater
```php
       /**
        * @EtEx(displayName="Service maître")
        * @ORM\Column(type="array", nullable=true)
        */
       private $headService;
```

- **Add sheets from other entities**
If your entity is linked with some others. For example your entity "Person" represents some persons, and some of them are musicians.  
You probably need some more informations about musicians wich are represented by the "Musician" entity.  
With the `includeFields` parameter, you will generate another sheet in your excel file with the properties of the Musician entity.  
```php
    /**
     * @EtEx(includeFields=true, displayName="Precision if this person is musician")
     * @ORM\OneToOne(targetEntity=Musician::class, cascade={"persist", "remove"})
     */
    private $musicianPrecision;
```  
# Future development
The reverse side of this program is on the way in order to import data following excel files.  
To be continued ...  
  
  Feel free to report any encoutered problemes using issues on github  
  Github repository : https://github.com/frvaillant/EntityToExcel/

