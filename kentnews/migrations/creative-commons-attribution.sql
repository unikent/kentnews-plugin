UPDATE
	wp_postmeta
SET
	meta_value = CASE
		WHEN meta_value = 'CC BY' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CC-BY' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CCBY' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CC BY 2.' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'BY 2.0' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CC BY 2.0' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CC BY 2.0)' THEN '<a href="https://creativecommons.org/licenses/by/2.0/" target="__blank">Attribution License</a>'
		WHEN meta_value = 'CC BY SA' THEN '<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="__blank">Attribution-ShareAlike License</a>'
		WHEN meta_value = 'BY-SA 2.0' THEN '<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="__blank">Attribution-ShareAlike License</a>'
		WHEN meta_value = 'by-sa' THEN '<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="__blank">Attribution-ShareAlike License</a>'
		WHEN meta_value = 'CC BY-SA' THEN '<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="__blank">Attribution-ShareAlike License</a>'
		WHEN meta_value = 'CC BY-SA 2.0' THEN '<a href="https://creativecommons.org/licenses/by-sa/2.0/" target="__blank">Attribution-ShareAlike License</a>'
		WHEN meta_value = 'CC BY-SA 3.0' THEN '<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="__blank">Attribution-ShareAlike 3.0 Unported License</a>'
		WHEN meta_value = 'CC-BY-SA-3.0' THEN '<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="__blank">Attribution-ShareAlike 3.0 Unported License</a>'
		WHEN meta_value = 'CC BY-SA 3.0' THEN '<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="__blank">Attribution-ShareAlike 3.0 Unported License</a>'
		WHEN meta_value = 'CC BY-NC' THEN '<a href="https://creativecommons.org/licenses/by-nc/2.0/" target="__blank">Attribution-NonCommercial License</a>'
		WHEN meta_value = 'CC BY NC' THEN '<a href="https://creativecommons.org/licenses/by-nc/2.0/" target="__blank">Attribution-NonCommercial License</a>'
		WHEN meta_value = 'CC BY NC-SA' THEN '<a href="https://creativecommons.org/licenses/by-nc-sa/2.0/" target="__blank">Attribution-NonCommercial-ShareAlike License</a>'
		WHEN meta_value = 'CC BY-NC-SA' THEN '<a href="https://creativecommons.org/licenses/by-nc-sa/2.0/" target="__blank">Attribution-NonCommercial-ShareAlike License</a>'
		WHEN meta_value = 'CC BY-NC-ND' THEN '<a href="https://creativecommons.org/licenses/by-nc-nd/2.0/" target="__blank">Attribution-NonCommercial-NoDerivs License</a>'
		WHEN meta_value = 'CC BY-NC-ND 2.0' THEN '<a href="https://creativecommons.org/licenses/by-nc-nd/2.0/" target="__blank">Attribution-NonCommercial-NoDerivs License</a>'
		WHEN meta_value = 'cc by-nc-nd' THEN '<a href="https://creativecommons.org/licenses/by-nc-nd/2.0/" target="__blank">Attribution-NonCommercial-NoDerivs License</a>'
		WHEN meta_value = 'cc by-nc-nd' THEN '<a href="https://creativecommons.org/licenses/by-nc-nd/2.0/" target="__blank">Attribution-NonCommercial-NoDerivs License</a>'
		WHEN meta_value = 'CC BY-ND' THEN '<a href="https://creativecommons.org/licenses/by-nd/2.0/" target="__blank">Attribution-NoDerivs License</a>'
		WHEN meta_value = 'CC NC ND' THEN '<a href="https://creativecommons.org/licenses/by-nc-nd/2.0/" target="__blank">Attribution-NonCommercial-NoDerivs License</a>'
		WHEN meta_value = 'CC SA' THEN '<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="__blank">Attribution-ShareAlike 3.0 Unported License</a>'
		WHEN meta_value = 'All rights reserved' THEN 'All Rights Reserved'
		ELSE meta_value
	END
