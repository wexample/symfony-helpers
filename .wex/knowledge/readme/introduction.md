### **Pseudocode Generator**

## **Overview**

The **Pseudocode Generator** is a tool that analyzes PHP code and produces a structured YAML representation of its
elements, including:

- Classes and their methods
- Functions
- Constants

In addition to extracting the code structure, it also gathers all related documentation, such as PHPDoc comments and
inline annotations.

## **Key Technical Details**

The purpose of pseudocode is to extract general information about the code without being tied to any specific underlying
technology. For example, a configuration generated from a Doctrine entity could be used to produce code for frameworks
like Prisma or SQLAlchemy.

### Configuration Objects

The system uses a chain of configuration objects to construct the final representation:

- **Individual Configuration:** Each code element (such as a class, property, or function) is managed by its own
  configuration class.
- **Contextual Reference:** Configuration objects can reference their parent context via the `parentConfig` parameter.
- **Recursive Assembly:** The complete output structure is built by recursively calling the `toConfig()` method on these
  objects.

### Testing

- **Test Command:** The command to run tests within the container is stored in the `TEST_COMMAND_SINGLE_FAIL` variable
  in the `.wex/.env` file, or ask administrator if not accessible.
- **Temporary Files:** During tests, generated files are saved in a temporary directory inside the container at
  `/tmp/pseudocode_tests`.
    - **Access Outside the Container:** To access this directory from outside, create a symbolic link that points from
      `/tmp/pseudocode_tests` to your package directory.

For example, run the following commands:

```bash
# Command to run **inside the container** to prepare test files exporting.
rm -rf /tmp/pseudocode_tests/
mkdir /var/www/html/vendor-local/wexample/pseudocode/tmp
ln -s /var/www/html/vendor-local/wexample/pseudocode/tmp /tmp/pseudocode_tests
```

### Adding a Configuration

- Good example of configuration file: `src/Config/DocCommentConfig.php`

#### Specify the Annotation

Define the annotation to be recognized. For example:

```php
#[ORM\Id]
```

#### Define the Pseudocode Output

Determine the corresponding pseudocode format. For example:

```yaml
item:
  - name: ...
    database:
      primary: true
```

#### Create the Configuration File

Add a configuration file in the `src/Config` directory. This file should be responsible for:

- Parsing the code to generate pseudocode.
- Converting the pseudocode back into actual code.
  ex: `src/Config/DatabasePrimaryConfig.php`

#### Register the configuration

Add the class in `src/Common/ConfigRegistry.php`

#### Test the configuration

Testing is not optional.

- Create a test which inherits from AbstractGeneratorTest in a specific folder `tests/Item/Database/DatabaseTest.php`
- Create two test files in `tests/Item/Database/resources/`
    - One php file containing an example code of the pattern you are working on
    - One yml file as the pseudocode that should be generated from the code
- The two files should be reversible: we should generate the php file based on the yml, and the yaml base on the php
    - Is some part, like functions body, wil not be generated, it should be marked with a TODO comment.
- See `Testing` section below for running test detail

---

This version should serve as a clear, concise, and accurate English rendition of the original text.