# Sid Lee Tools - Groomer

- [Introduction](#introduction)
- [Purpose](#purpose)
- [Structure](#structure)
- [How To Use](#how-to-use)
- [Examples](#examples)
- [Composer](#composer)

## Introduction
The **Groomer** module provides a service that can read and process a Drupal Object, interpret its data, and output it in a clean format.

## Purpose

The ultimate goal of this module is to streamline the pre-processing Drupal Object data.

Entities & Fields were the main priority. For any given Entity, developers should be able to access all of the entity's data and field values seamlessly.

## Structure

To understand this module's structure, you must first understand what a **Groomer** really is.

A **Groomer** is an object associated to an instance of a Drupal Object that analyzes and outputs its data in a clean uniform format.

The best example is a Drupal Entity Groomer. Say you have a Node object of type Basic Page with many fields. A Groomer applied to a Node will recursively read through the Node's fields and output the data found into a clean array format that is easily readable. Additionally it exposes a variable called `data` in the Node's `node--basic_page.html.twig` template, containing all of the clean data easily accessible.

The module exposes Groomers for all of the common **Entity Types** and their **Fields**.

### Groomer Signature Concept

It's important to note that each Groomer has a **Signature**. The Signature represents the identification of a Groomer as well as the indication of **what** it's actually grooming. An example signature would be `entity.node.basic_page`, indicating that the Groomer is grooming an **Entity**, of type **Node**, with the **Basic Page** bundle. Another example would be `field.email.field_email`. This applies to a **Field**, of type **Email**, with the machine name **field_email**.

Each ( ***.*** ) in a groomer signature separates a granularity. This means that when using any of the modules features, if you target a signature like `entity.node`, it works and will target all **Nodes** on your site. This is important to note for when you start using **Refiners** & **Harmony**. You'll learn more about those later.

### Work In Progress
- Groomers for **Menus**.
  - Working, but needs a lot more work and cleanup to be complete and usable.
- Groomers for **Forms**.
- Groomers for **Views**.

## How To Use

### Using the Groomer Service in entity pre-processing
If you enable the module, the functionality won't be used automatically. You can only really see the results if you use the Groomer service in the `.theme` file of your custom theme.

In the said file, here is an example snippet you could add.

```php
/**
 * Implements hook_preprocess_HOOK() for page document templates.
 */
function THEMENAME_preprocess_node(&$variables) {
  if ($variables['node']->bundle() === 'basic_page') {
    $variables['data'] = \Drupal::service('groomer.manager')->groom($variables['node']);
  }
}
```

With this example, in your `node--basic_page.tpl.twig`, you will now have an available `data` variable that contains the groomed data. Take a look at how the fields come out!

### Activating Auto Pre-Processing
Visit the configuration page at `/admin/config/groomer/configuration` to activate Auto Pre-Processing.

This makes it so that all basic entities (Nodes, Paragraphs & Media) will be processed automatically and should have an available variable called `data` in their template files without having to add any code to your `.theme` file.

### Using Groomer Refiners

By default, Groomer processes and outputs it's data in the same way no matter what kind of object or entity is given. That means the output is pretty streamlined throughout the site. But there are options for customization

**Refiners** are one of these options. They are **Plugins** that can be implemented to alter the data a Groomer outputs. This is symbolic to Drupal's native `hook_alter` functions.

To use them, implement a Plugin that extends the `\Drupal\groomer\PluginManager\Refinery\RefinerBase` class, and implement a public `alter($data, $original_object)` function. You must add an annotation that defines the plugin targets a specific Groomer using the **Groomer Signature Concept**. Rearrange the data found in the `$data` variable to your liking, and it will be adjusted accordingly before hitting theme templates.

Refer to the **groomer_examples** module for working examples. You can base yourself on this code to create your own Refiners that alter the data output for your site's object types.

You can use Refiners to add some personalization to the Groomer Output **before** it reaches ***Harmony***(See following section) as well.

### Using Harmony
**Harmony** is a strategy that involves adding a directory to your theme that allows for overrides of data passed by Groomers in a highly segmented/organized collection of files.

To get started, visit the configuration page at `/admin/config/groomer/configuration`.
Then, create a `./themes/custom/SITE_THEME/harmony` folder at the root of your **custom theme**.

This folder can be seen as the "agreement" between Front-End & Back-End teams. Here, a `*.harmony.php` file can be created for a given entity or object. This file only needs to contain php code that manipulates 2 variables:

- `$groomer_data` - The data returned by the Groomer's first pre-processing.
- `$data` - The data that is sent to the templates.

By the end of the code ran in the file, everything that should be passed to templates should be in the `$data` variable.

The names of the files created determines which Drupal object is targetted for overriding. The names use the **Groomer Signature Concept**, that you can read about up above. Using this concept, you can create files with names such as **entity.harmony.php**, **entity.node.harmony.php** or **entity.node.harmony.page.php** to target respective granularities in entities and objects. Two example files can be found in the tools folder of this module.

#### Example

You can use the following snippet as a template. This would apply to a **node** of type **page**.

```php
<?php
/**
 * @file
 * Override data for a given groomer.
 *
 * @groomer_type  entity
 * @entity_type   node
 * @bundle        page
 *
 * This is a custom file to handle the reformatting of data before it reaches
 * Drupal templates.
 *
 * Available variables:
 * - $groomer_data
 *    The data coming from the groomer.
 * - $data
 *    The data that will be returned to the Twig template.
 *
 * Re-arrange and assign data to the $data variable here, and that's all you
 * need. The rest will be handled in the Twig Templates.
 */

// Alter the data. Anything found in $data is sent to front-end templates.
// $data['completely_reformatted'] = 'Hi! I\'ve been completely reformatted!';

// If $data is never set, anything found in $groomer_data is sent instead.
// $groomer_data['entity.harmony'] = 'Applied. Hey there!';
```

### Customizing Groomed Fields

Further customization for this is possible. In the `/admin/config/groomer/configuration` page, you can customize which fields are groomed for every given entity type and even every bundle.

This allows you to disable grooming on certain fields, which will omit them from the output altogether.

### Groomer Rest API
***UNDER CONSTRUCTION***

## Examples

Many examples can be found or taken directly from the documentation. Have a look at the many READMEs found throughout the module's code.

### Quickstart Example Guide
Wanna see this module in action quick? Well, if you're starting off a fresh installation, it's gonna take some setting up, as the module needs working entities. Here's a list of steps.

1. Create a content type and add some fields to test with.
2. Go to the configuration page for the groomer. `/admin/config/groomer/configuration`
3. Activate Auto Pre-Processing (Or, if you prefer, simply copy the hook example above)
4. Now in the template file associated to this entity type, you should have data in a variable called...***data***. Dump it and check out the contents!

***An example module will be created at some point to properly automate this process***

## Composer
Make sure to add this to your "repositories" entries in your root composer.json file:

```
{
    "type": "vcs",
    "url": "sidlee@vs-ssh.visualstudio.com:v3/sidlee/Internal-Drupal_Modules/groomer"
}
```
