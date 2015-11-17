<?php echo $header?>

<script>
function togglePerson(person)
{
    primarypeople = document.getElementsByClassName('primaryperson');

    if(primarypeople.length == 0){
        person.classList.add('primaryperson');
    } else if(!person.classList.contains('primaryperson')){
        person.classList.toggle('selectedperson');
    } else {
        person.classList.toggle('primaryperson');
    }
    
    selectedpeople = document.getElementsByClassName('selectedperson');

    if(selectedpeople.length == 0){
        mergebutton = document.getElementById('mergebutton');
        mergebutton.classList.add('disabled');
    } else {
        mergebutton = document.getElementById('mergebutton');
        mergebutton.classList.remove('disabled');

        primarypeople = document.getElementsByClassName('primaryperson');
        if(primarypeople.length == 0){
            selectedpeople[0].classList.add('primaryperson');
            selectedpeople[0].classList.remove('selectedperson');
        }
    }

    return false;
}
function doMerge()
{
    mergebutton = document.getElementById('mergebutton');
    if(mergebutton.classList.contains('disabled')){
        return false;
    }

    primaryList = document.getElementsByClassName('primaryperson');
    primary = primaryList[0].dataset.id;
    people = document.getElementsByClassName('selectedperson');
    var list = [];
    for (i = 0; i < people.length; i++) {
        list.push(people[i].dataset.id);
    }

    var xhttp = new XMLHttpRequest();

    
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
            '<img class="u-img" src="' + mergedPerson.image + '" alt="representative image of ' + mergedPerson.name + '" />' +
            '<span class="p-name">'+  mergedPerson.name + '</span><br> ' +
            '<a class="u-url" href="' + mergedPerson.url + '">' + mergedPerson.url + '</a><br>';


            for (i = 0; i < mergedPerson.alternates.length; i++) {
                newhtml = newhtml + ' <a class="u-x-alternate-url" href="' + mergedPerson.alternates[i].url + '">' + mergedPerson.alternates[i].url + '</a><br>'
            }

        elem.innerHTML = newhtml;
      }
    }
     
    xhttp.open("POST", "/people/merge", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('primary='+primary+'&list='+list);

    people = document.getElementsByClassName('selectedperson');
    var list = [];
    for (i = 0; i < people.length; i++) {
        people[i].classList.remove('selectedperson');
    }
    people = document.getElementsByClassName('primaryperson');
    var list = [];
    for (i = 0; i < people.length; i++) {
        people[i].classList.remove('primaryperson');
    }
    mergebutton = document.getElementById('mergebutton');
    mergebutton.classList.remove('disabled');
    
}

</script>

<div id="peoplecontrols">
<button id="mergebutton" class="disabled" onclick="doMerge()">Merge</button><br>
<?php if(isset($next)){ ?>
<a id="nextbutton" href="<?php echo $next ?>"><button>Next</button></a><br>
<?php } ?>
<?php if(isset($prev)){ ?>
<a id="prevbutton" href="<?php echo $prev ?>"><button>Prev</button></a><br>
<?php } ?>
</div>

<div id="people">
  <?php foreach($people as $person){?>
  <div class="h-card person"
        id="person-<?php echo $person['person_id']?>" 
        data-id="<?php echo $person['person_id']?>" 
        onclick="togglePerson(this)">

      <img class="u-img" src="<?php echo $person['image']?>" />
      <span class="p-name"><?php echo $person['name'] ?></span><br>
      <a class="u-url" href="<?php echo $person['url'];?>"><?php echo $person['url'] ?></a><br>
      <?php foreach($person['alternates'] as $alt){ ?>
          <a class="u-x-alternate-url" href="<?php echo $alt['url'];?>"><?php echo $alt['url'] ?></a><br>
      <?php } // end foeach alternate ?>
  </div>
  <?php } // end foeach ?>
</div>


<?php echo $footer?>
