<?
    // OBLICZANIA DLA OKRESLONEJ DATY
    $d = $_GET['d'] ;
    $m = $_GET['m'] ;
    $r = $_GET['y'] ;
    
    $zyjesz = (time()-mktime(0,0,1,$m,$d,$r))/86400.0 ;
    $normalnie = 1;

    if ($zyjesz<0.0 && $normalnie) form(4);
    else
    {
	if ($zyjesz<0.0) {
           $form2 = 4;
           $zyjesz = (time()-mktime(0,0,1,$m,$d,$r))/86400.0;
           $normalnie = 1;
	}

        $PI_2 = M_PI+M_PI; // 2*pi
        settype($zyjesz,'integer');
	$start = 0-(date("d")) ;
	$end = 33 + $start ;
	$a=-1 ;
	for($i=$start; $i<$end; $i++ ) {
	    $tend_int = $PI_2*((($zyjesz+($i))%33)/33.0);
    	    $intelektualny[$a] = sin($tend_int)*(-1);
    	    $tend_emo = $PI_2*((($zyjesz+($i))%28)/28.0);
    	    $emocjonalny[$a] = sin($tend_emo)*(-1);
    	    $tend_fiz = $PI_2*((($zyjesz+($i))%23)/23.0);
	    $fizyczny[$a] = sin($tend_fiz)*(-1);
	    $a++ ;
	}
    }

    Header("Content-Type: image/png");
    $szerokosc = 280 ;
    $wysokosc = 120 ;
// rysowanie tabelki ..    
    $obraz = ImageCreate($szerokosc, $wysokosc) ;
    $kolor_tla = ImageColorAllocate($obraz, 255, 255, 255) ;
    $kolor_tekstu = ImageColorAllocate($obraz, 0, 0, 255) ;
    $kolor_emo = ImageColorAllocate($obraz, 0, 0, 255) ; // blue
    $kolor_int = ImageColorAllocate($obraz, 0, 125, 0) ; // green
    $kolor_fiz = ImageColorAllocate($obraz, 255, 0, 0) ; // red
    $kolor_siatki_sz = ImageColorAllocate($obraz, 200, 200, 200) ;
    $kolor_siatki_cz = ImageColorAllocate($obraz, 0, 0, 0) ;
    $kolor_wykresu = ImageColorAllocate($obraz, 35, 81, 129) ;
    ImageFill($obraz, 100, 100, $kolor_tla) ;

// rysowanie siatki
    ImageLine($obraz, 0, $wysokosc/2, $szerokosc, $wysokosc/2, $kolor_siatki_cz) ;
    for($i=1; $i<32; $i++) {
	ImageLine($obraz, (4+($i*8)), ($wysokosc/2)-2, (8+($i*8)-4), ($wysokosc/2)+2, $kolor_siatki_sz) ;
    }
    ImageLine($obraz, (4+(date("d")*8)), 1, (8+(date("d")*8)-4), ($wysokosc-1), $kolor_siatki_sz) ;
    
    for($i=0; $i<32; $i++) {
	ImageLine($obraz, (4+($i*8)), (($wysokosc-2)/2)+($fizyczny[$i]*(($wysokosc-2)/2)),(4+(($i-1)*8)), (($wysokosc-2)/2)+($fizyczny[($i-1)]*(($wysokosc-2)/2)), $kolor_fiz) ;
	ImageLine($obraz, (4+($i*8)), (($wysokosc-2)/2)+($intelektualny[$i]*(($wysokosc-2)/2)),(4+(($i-1)*8)), (($wysokosc-2)/2)+($intelektualny[($i-1)]*(($wysokosc-2)/2)), $kolor_int) ;
	ImageLine($obraz, (4+($i*8)), (($wysokosc-2)/2)+($emocjonalny[$i]*(($wysokosc-2)/2)),(4+(($i-1)*8)), (($wysokosc-2)/2)+($emocjonalny[($i-1)]*(($wysokosc-2)/2)), $kolor_emo) ;
    }
    ImagePNG($obraz) ;
    ImageDestroy($obraz) ;
?>
