<?php echo $header?>

<script>
function togglePerson(person)
{
    primarypeople = document.getElementsByClassName('primaryperson');

    if(primarypeople.length == 0){
        person.classList.add('primaryperson');
    } else if(!person.classList.contains('primaryperson')){
        person.classList.toggle('selectedperson');
    }
    return false;
}
function primaryPerson(person)
{
    person.classList.remove('selectedperson');
    primarypeople = document.getElementsByClassName('primaryperson');
    for (i = 0; i < primarypeople.length; i++) {
        primarypeople[i].classList.add('selectedperson');
        primarypeople[i].classList.remove('primaryperson');
    }
    person.classList.add('primaryperson');

    return false;
}
function doMerge()
{
    primaryList = document.getElementsByClassName('primaryperson');
    primary = primaryList[0].dataset.id;
    people = document.getElementsByClassName('selectedperson');
    var list = [];
    for (i = 0; i < people.length; i++) {
        list.push(people[i].dataset.id);
    }

    var xhttp = new XMLHttpRequest();

    /*
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
        var result = JSON.parse(xhttp.responseText);

        listElem = document.getElementById('people');

        delList = result.removeList
        for (i = 0; i < delList.length; i++) {
          listElem.removeChild(document.getElementById('person-' + delList[i]));
        }

        mergedPerson = result.updated;

        elem = document.getElementById('person-' + mergedPerson.person_id);

        var newhtml = 
            '<img class="u-img" src="' + mergedPerson['image' + '" alt="representative image of ' + mergedPerson.name + '" />' +
            '<span class="p-name">'+  mergedPerson.name + '</span><br> '+
            '<a class="u-url" href="' + mergedPerson.url + '">' + mergedPerson.url + '</a><br>';


            for (i = 0; i < mergedPerson.alternates.length; i++) {
                newhtml = newhtml + ' <a class="u-x-alternate-url" href="' + mergedPerson.alternates[i].url + '">' + mergedPerson.alternates[i].url + '</a><br>'
            }

        elem.innerHTML(newhtml);
      }
    }
     */
    xhttp.open("POST", "/people/merge", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('primary='+primary+'&list='+list);
    
}
</script>

<div id="people">
  <?php foreach($people as $person){?>
  <div class="h-card person"
        id="person-<?php echo $person['person_id']?>" 
        data-id="<?php echo $person['person_id']?>" 
        onclick="togglePerson(this)">

      <img class="u-img" src="<?php echo $person['image']?>" alt="representative image of <?php echo $person['name']?>" />
      <span class="p-name"><?php echo $person['name'] ?></span><br>
      <a class="u-url" href="<?php echo $person['url'];?>"><?php echo $person['url'] ?></a><br>
      <?php foreach($person['alternates'] as $alt){ ?>
          <a class="u-x-alternate-url" href="<?php echo $alt['url'];?>"><?php echo $alt['url'] ?></a>
      <?php } // end foeach alternate ?>
      <button class="primarycontrol" onclick="primaryPerson(this.parentElement); return false;" >P</button>
  </div>
  <?php } // end foeach ?>
</div>

<?php echo $footer?>
