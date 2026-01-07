1. HTML Lists

HTML lists represent collections of related items. They are widely used in navigation menus, feature lists, FAQs, and structured content. Understanding lists deeply is essential for professional web development.

1.1 List Types and Core Tags
List Types

Unordered List – <ul> (bulleted items)

Ordered List – <ol> (numbered / lettered items)

List Item – <li> (individual item inside <ul> or <ol>)

Description List – <dl> with:

<dt> → term

<dd> → description

Why These Tags Exist

<ul> → when order does not matter (features, menus)

<ol> → when order matters (steps, rankings)

<li> → gives semantic meaning to items

<dl>, <dt>, <dd> → term–definition or key–value data

1.2 Global Attributes (All List Tags)

All list-related elements support global attributes:

id – unique identifier

class – styling or JS hooks

style – inline CSS (avoid in production)

title – tooltip text

hidden – hides element

lang, data-*, etc.

1.3 Unordered Lists (<ul> and <li>)
Syntax
<ul>
  <li>First item</li>
  <li>Second item</li>
  <li>Third item</li>
</ul>

Default Output

Bullet points (disc)

Each <li> on a new line

Notes

<ul> and <li> have no special attributes

Old type attribute is obsolete → use CSS (list-style-type)

Use Cases

Feature lists

Navigation menus

Any unordered group of items

Example: Navigation Menu
<nav>
  <ul>
    <li><a href="index.html">Home</a></li>
    <li><a href="services.html">Services</a></li>
    <li><a href="contact.html">Contact</a></li>
  </ul>
</nav>

1.4 Ordered Lists (<ol> and <li>)
Syntax
<ol>
  <li>Turn on the computer</li>
  <li>Open the browser</li>
  <li>Visit the website</li>
</ol>

<ol> Attributes
Attribute	Values	Purpose
type	1, A, a, I, i	Marker style
start	Number	Starting value
reversed	Boolean	Reverse numbering
Example: Custom Type and Start
<ol type="A" start="3">
  <li>Item C</li>
  <li>Item D</li>
</ol>

Example: Reversed List
<ol reversed>
  <li>Gold</li>
  <li>Silver</li>
  <li>Bronze</li>
</ol>

Use Cases

Tutorials

Rankings

Step-by-step instructions

1.5 Description Lists (<dl>, <dt>, <dd>)
Syntax
<dl>
  <dt>HTML</dt>
  <dd>The standard markup language for web pages.</dd>

  <dt>CSS</dt>
  <dd>The language that styles HTML elements.</dd>
</dl>

Default Behavior

<dt> appears bold

<dd> is indented

Multiple <dd> allowed per <dt>

Use Cases

Glossaries

FAQs

Key–value pairs

1.6 Nested Lists
<ul>
  <li>Frontend
    <ul>
      <li>HTML</li>
      <li>CSS</li>
      <li>JavaScript</li>
    </ul>
  </li>
  <li>Backend
    <ul>
      <li>Node.js</li>
      <li>Python</li>
    </ul>
  </li>
</ul>


Used to represent hierarchical data.

1.7 Accessibility & SEO (Lists)

Use lists only when content is actually a list

Avoid fake lists using <br> or <p>

Screen readers announce item count

Improves navigation and SEO

1.8 Common List Mistakes

Text directly inside <ul> or <ol> without <li>

Using lists purely for layout

2. HTML Images (<img>)

The <img> tag embeds images via URLs. It is a void element (no closing tag).

2.1 Basic Syntax
<img src="profile.jpg" alt="Profile photo of John Doe">


Displays image

Shows alt text if image fails

2.2 Core Attributes
Attribute	Required	Purpose
src	Yes	Image path
alt	Yes	Accessibility & SEO
width	No	Render width
height	No	Render height
Example
<img src="landscape.jpg" alt="Mountain sunset" width="400" height="250">

2.3 Advanced & Modern Attributes
Attribute	Purpose
srcset	Responsive images
sizes	Layout width hint
loading	lazy or eager
decoding	Image decode strategy
referrerpolicy	Privacy control
crossorigin	CORS handling
usemap, ismap	Image maps
2.4 Responsive Image Example
<img
  src="profile-800.jpg"
  srcset="
    profile-400.jpg 400w,
    profile-800.jpg 800w,
    profile-1200.jpg 1200w
  "
  sizes="(max-width: 600px) 100vw, 600px"
  alt="Profile photo of a software developer"
>

2.5 Performance Tips
<img src="banner.jpg" alt="Banner" loading="lazy">


Loads only when needed

Improves page speed

2.6 Accessibility Best Practices

Informative image → meaningful alt

Decorative image → alt=""

3. HTML Tables

Tables are for tabular data only, not layout.

3.1 Core Structure
<table>
  <tr>
    <th>Language</th>
    <th>Level</th>
  </tr>
  <tr>
    <td>HTML</td>
    <td>Beginner</td>
  </tr>
</table>

3.2 Structural Elements
Element	Purpose
<caption>	Table title
<colgroup> / <col>	Column styling
<thead>	Header rows
<tbody>	Data rows
<tfoot>	Footer rows
3.3 <th> Attributes
Attribute	Purpose
scope	Defines header relation
abbr	Accessibility abbreviation
3.4 Fully Structured Table Example
<table>
  <caption>Monthly Expense Summary</caption>
  <thead>
    <tr>
      <th scope="col">Category</th>
      <th scope="col">Planned</th>
      <th scope="col">Actual</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Rent</th>
      <td>$500</td>
      <td>$500</td>
    </tr>
  </tbody>
</table>

3.5 Table Accessibility

Always use <th> for headers

Add <caption>

Use scope for clarity

3.6 Common Table Mistakes

Tables for layout

Missing <caption>

Incorrect <th> usage

4. Quick Revision Summary
Lists

<ul> → unordered

<ol> → ordered (type, start, reversed)

<dl> → term–description

Images

Minimum: <img src="" alt="">

Use srcset, sizes, loading="lazy"

Tables

Core: <table>, <tr>, <th>, <td>

Advanced: <caption>, <thead>, <tbody>, <tfoot>