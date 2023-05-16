import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { SearchHotelsRequest } from '../_models/Hotels/SearchHotelsRequest';
import { HotelInfoResponse } from '../_models/Hotels/HotelInfoResponse';
import { HotelOffer } from '../_models/Hotels/HotelOffer';
import { HotelResponse } from '../_models/Hotels/HotelResponse';

@Injectable({
  providedIn: 'root'
})
export class HotelService {
  private apiUrl = environment.apiUrl + "/hotel";

  httpOptions = {};

  constructor(private http: HttpClient) { }

  getHotels(body: SearchHotelsRequest): Observable<HotelResponse[]> {
    return this.http.post<HotelResponse[]>(this.apiUrl + "/searchSelectHotel", JSON.stringify(body), this.httpOptions);
  }

  getHotelInfo(hotelOffer: string): Observable<HotelInfoResponse> {
    return this.http.get(this.apiUrl + "/reviewSelectedHotelOfferInfo/" + hotelOffer);
  }

  getHotelConfirmation(bookingInfo: HotelOffer): Observable<{}> {
    return this.http.post<HotelOffer>(this.apiUrl + "/confirmFlight", JSON.stringify(bookingInfo), this.httpOptions);
  }
}