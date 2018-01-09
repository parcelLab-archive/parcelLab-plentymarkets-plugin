<div class="alert alert-warning" role="alert">
   <strong><i>Note:</i></strong> The parcelLab plugin has been developed for use with the online store Ceres and only works with its structure or other template plugins.
</div>

# parcelLab – Tailored shipping notifications for your shop

With custom shipping notifications you create valuable customer touch points which would otherwise be wasted to UPS, FedEx & Co.

## Get in touch

Before you can set up the shipping notifications in plentymarkets, you need to [register at parcelLab] (https://parcellab.com/). You will then receive information as well as access data that you need for the setup.

## Plugin installation

Before the module can be used, it must be installed in plentymarkets.

**Installation of the parcelLab plugin via plentyMarketplace:**

1. Open [plentyMarketplace] (https://marketplace.plentymarkets.com/) in the browser
2. Find the plugin under **Integration** → **ParcelLab**
3. **Go to checkout** (Login) and confirm purchase
4. Navigate to the backend from the shop
5. Open the menu **Plugins » Purchases**
6. Click the **Install** button for the parcelLab plugin

**Installation of the parcelLab plugin via GIT:**

1. Open the menu **Plugins » Git**
2. Select **New Plugin**. The **Settings** window opens.
3. Connect your GitHub access and enter **Username** and **Password**
4. Enter the remote URL of the parcelLab plugin: <https://github.com/parcelLab/parcelLab-plentymarkets-plugin.git>
5. Enable **Auto fetch**
6. Finally, save with **Save**

## Setup parcelLab in plentymarkets

Before you can use the features of the parcelLab plugin, you must first connect your parcelLab account to your plentymarkets system.

##### parcelLab account setup:

1. Open the **Plugins » Overview** menu.
2. Click the **ParcelLab** plugin, then select **Configuration**.
3. Enter the respective values ​​in the fields _parcelLab ID_ and _parcelLab Token_.<br />
    → You will get this information after contacting parcelLab.
4. Save the settings.

<table>
  <caption>Tab. 1: parcelLab Plugin Settings / Default</caption>
  <thead>
    <th>
      Setting
    </th>
    <th>
      Explanation
    </th>
  </thead>
  <tbody>
    <tr>
      <td>
        <b>parcelLab ID</b>
      </td>
      <td>
      	 User for authentication
      </td>
    </tr>
    <tr>
      <td>
        <b>parcelLab Token</b>
      </td>
      <td>
      	 Token for authentication
      </td>
    </tr>
  </tbody>
</table>

## Integration: Shipping notifications

Set up an event procedure to automate the creation of shipping notifications. Note: shipping notifications without package number will be denied.

##### Set up the event procedure:

1. Open the menu **Settings » Orders » Event procedures**.
2. Click **Add event procedure**.<br />
    → The window **Create new event procedure** is displayed.
3. Enter a name.
4. Select the event as shown in table 2.
5. **Save** the settings.
6. Make the settings as shown in table 2.
7. Set a check mark to **Active**.
8. **Save** the settings.

<table>
  <thead>
    <th>
      Setting
    </th>
    <th>
      Option
    </th>
    <th>
      Choice
    </th>
  </thead>
  <tbody>
    <tr>
      <td><strong>Event</strong></td>
      <td>Order change > Outgoing items booked</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><strong>Filter 1</strong></td>
      <td>Order > Order type</td>
      <td>Order</td>
    </tr>
    <tr>
      <td><strong>Action</strong></td>
      <td>Plugin > parcelLab | Create Trackings</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
  <caption>
    Tab. 2: Event procedure for creation of shipping notifications
  </caption>
</table>

---------------------------------------

For more information on all topics, please visit the [parcelLab Docs] (https://docs.parcellab.com/).
