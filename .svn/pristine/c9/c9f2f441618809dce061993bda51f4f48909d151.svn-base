<?php
/**
 * 샘플 워터마크 클래스
 * 이 클래스에서는 이미지 배경색과 테두리 선을 추가하는 예를 보여줍니다.
 * preprocess 및 postprocess 함수에서는 Thumbnail 클래스에서 지정한 파라메터를 넘겨 받습니다.
 */
class ThumbnailWatermark
{
	/**
	 * Thumbnail 클래스에서 preprocess 및 postprocess 함수 호출시 넘겨주는 파라메터들
	 *
	 * @param	Resource	$resource		GD Image 함수용 섬네일 리소스
	 * @param	Number		$thumb_width	섬네일 넓이
	 * @param	Number		$thumb_height	섬네일 높이
	 * @param	Number		$image_width	섬네일 안에서 축소된 이미지의 넓이
	 * @param	Number		$image_height	섬네일 안에서 축소된 이미지의 높이
	 * ※만약, 섬네일 scale 이 SCALE_SHOW_ALL 일 경우, 섬네일 크기보다 이미지가 작아질 수 있습니다.
	 * @return	void
	 */
	public function preprocess($resource, $thumb_width, $thumb_height, $image_width, $image_height)
	{
		// 입력한 색상으로 전체 이미지를 칠한다.
		$color = ImageColorAllocate($resource, 240, 240, 240);
		ImageFilledRectangle($resource, 0, 0, $thumb_width, $thumb_height, $color);

		return $resource;
	}

	public function postprocess($resource, $thumb_width, $thumb_height, $image_width, $image_height)
	{
		$color = ImageColorAllocate($resource, 0, 0, 0);
		ImageLine($resource, 0, 0, $thumb_width - 1, 0, $color);
		ImageLine($resource, $thumb_width - 1, 0, $thumb_width - 1, $thumb_height - 1, $color);
		ImageLine($resource, $thumb_width - 1, $thumb_height - 1, 0, $thumb_height - 1, $color);
		ImageLine($resource, 0, $thumb_height - 1, 0, 0, $color);

		return $resource;
	}
}// END: class ThumbnailWatermark
?>