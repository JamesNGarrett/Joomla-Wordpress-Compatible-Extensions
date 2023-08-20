# Joomla-Wordpress-Compatible-Extensions
Attempting to set up extensions that install into both Joomla and Wordpress

The idea is to make some skeleton extensions that install into both Joomla and Wordpress, ie a single zip works in both.

1. Module Widget

- Displays in the front end
- Small form in the backend to change the title and body

2. Content Plugin Shortcode


### Approaches and Problems

Either extensions are written to work in both or the Joomla extension is written as is and then a wordpress decorator is added, either in the entry file or alongside?

Can enough Joomla JForm code be used such that an xml field set could be used for wordpress settings? Maybe easier to just build a custom form renderer to work with a subset of Joomla standard form fields.

